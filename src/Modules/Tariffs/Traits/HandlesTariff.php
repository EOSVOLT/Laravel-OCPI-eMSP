<?php

namespace Ocpi\Modules\Tariffs\Traits;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ocpi\Models\PartyRole;
use Ocpi\Models\Tariffs\Tariff;
use Ocpi\Modules\Tariffs\Client\V2_2_1\CPOClient;
use Ocpi\Modules\Tariffs\Events\EMSP\TariffCreated;
use Ocpi\Modules\Tariffs\Events\EMSP\TariffReplaced;
use Ocpi\Modules\Tariffs\Repositories\TariffRepository;
use Ocpi\Support\Enums\Role;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Throwable;

trait HandlesTariff
{
    public function getTariffPath(string $version): string
    {
        return 'ocpi/cpo/' . $version . '/tariffs';
    }

    public function searchTariff(PartyRole $partyRole, ?string $externalId, bool $withTrashed = true): ?Tariff
    {
        $tariff = Tariff::query()
            ->where('party_id', $partyRole->party_id)
            ->when(null !== $externalId, function ($query) use ($externalId) {
                $query->where('external_id', $externalId);
            })
            ->withTrashed($withTrashed)
            ->first();
        if ($tariff === null) {
            return null;
        }

        return $tariff;
    }

    /**
     * @param PartyRole $partyRole
     * @param string $tariffExternalId
     * @param array $payload
     *
     * @return Tariff|null
     * @throws BindingResolutionException
     */
    public function tariffCreate(PartyRole $partyRole, string $tariffExternalId, array $payload): ?Tariff
    {
        if (empty($payload['elements']) || $tariffExternalId !== $payload['id']) {
            return null;
        }
        $tariffRepository = app()->make(TariffRepository::class);
        $createdTariff = $tariffRepository->createOrUpdateFromArray($partyRole->party_id, $payload);
        TariffCreated::dispatch($createdTariff->getId());
        return Tariff::query()->find($createdTariff->getId());
    }

    /**
     * @param Tariff $tariff
     * @param array $payload
     *
     * @return Tariff|null
     * @throws BindingResolutionException
     */
    public function tariffReplace(Tariff $tariff, array $payload): ?Tariff
    {
        if (empty($payload['elements']) || $tariff->external_id !== $payload['id']) {
            return null;
        }
        $tariffRepository = app()->make(TariffRepository::class);
        $updatedTariff = $tariffRepository->createOrUpdateFromArray($tariff->party_id, $payload);
        TariffReplaced::dispatch($updatedTariff->getId());
        return Tariff::query()->find($updatedTariff->getId());
    }

    /**
     * @throws Throwable
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function fetchFromCPO(?string $partyCode = null): void
    {
        $activityId = time() . rand(1000, 9999);
        Log::channel('ocpi')->info('ActivityId: ' . $activityId . ' | Starting OCPI Tariffs synchronization');

        $partyRoles = PartyRole::query()
            ->withWhereHas('party', function ($query) use ($partyCode) {
                $query->when(null !== $partyCode, function ($query) use ($partyCode) {
                    $query->whereIn('code', explode(',', $partyCode));
                });
                $query->where('is_external_party', true);
            })
            ->withWhereHas('tokens', function ($query) {
                $query->where('registered', true);
            })
            ->whereHas('parent_role', function ($query) {
                $query->where('role', Role::EMSP);
            })
            ->where('role', Role::CPO)
            ->get();

        if (0 === $partyRoles->count()) {
            Log::channel('ocpi')->error('ActivityId: ' . $activityId . ' | No Party to process.');
            return;
        }


        foreach ($partyRoles as $role) {
            $party = $role->party;
            Log::channel('ocpi')->info('ActivityId: ' . $activityId . ' | Processing Party ' . $party->code);
            $ocpiClient = new CPOClient($role->tokens->first());

            if (empty($ocpiClient->resolveBaseUrl())) {
                Log::channel('ocpi')->warning(
                    'ActivityId: ' . $activityId . ' | Party ' . $party->code
                    . ' is not configured to use the Tariffs module.'
                );
                continue;
            }

            foreach ($party->roles as $partyRole) {
                Log::channel('ocpi')->info(
                    'ActivityId: ' . $activityId . ' | - Call '
                    . $partyRole->code . ' / ' . $partyRole->country_code . ' - OCPI - Tariff GET'
                );
                $offset = 0;
                $limit = 100;
                do {
                    $ocpiTariffList = $ocpiClient->tariffs()->get(offset: $offset, limit: $limit);
                    $data = $ocpiTariffList?->getData() ?? [];
                    $tariffProcessedList = [];

                    Log::channel('ocpi')->info(
                        'ActivityId: ' . $activityId . ' | - ' . count($data) . ' Tariff(s) retrieved'
                    );
                    foreach ($data as $ocpiTariff) {
                        $ocpiTariffId = $ocpiTariff['id'] ?? null;

                        DB::connection(config('ocpi.database.connection'))->beginTransaction();

                        $tariff = $this->searchTariff(
                            $partyRole,
                            $ocpiTariffId,
                        );

                        Log::channel('ocpi')->info(
                            'ActivityId: ' . $activityId . ' |  > Processing '
                            . ($tariff === null ? 'new' : 'existing') . ' Tariff ' . $ocpiTariffId
                        );
                        // New Tariff.
                        if (null === $tariff) {
                            if (!$this->tariffCreate(
                                $partyRole,
                                $ocpiTariffId,
                                $ocpiTariff,
                            )) {
                                Log::channel('ocpi')->error(
                                    'ActivityId: ' . $activityId . ' | Error creating Tariff ' . $ocpiTariffId . '.'
                                );
                                DB::connection(config('ocpi.database.connection'))->rollback();

                                continue;
                            }

                        } else {
                            // Replaced Tariff.
                            if (!$this->tariffReplace(
                                $tariff,
                                $ocpiTariff
                            )) {
                                Log::channel('ocpi')->error(
                                    'ActivityId: ' . $activityId . ' | Error replacing Tariff ' . $ocpiTariffId . '.'
                                );
                                DB::connection(config('ocpi.database.connection'))->rollback();

                                continue;
                            }
                        }

                        $tariffProcessedList[] = $ocpiTariffId;

                        DB::connection(config('ocpi.database.connection'))->commit();
                    }
                    $offset += $limit;
                } while (null !== $ocpiTariffList->getLink());

                Log::channel('ocpi')->info(
                    'ActivityId: ' . $activityId . ' | - ' . count($tariffProcessedList) . ' Tariff(s) synchronized'
                );
            }
        }
    }
}