<?php

namespace Ocpi\Modules\Cdrs\Factories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Ocpi\Modules\Cdrs\Objects\Cdr;
use Ocpi\Modules\Cdrs\Objects\CdrCollection;
use Ocpi\Modules\Cdrs\Objects\CdrDetails;
use Ocpi\Modules\Tariffs\Factories\TariffFactory;
use Ocpi\Modules\Tariffs\Objects\TariffCollection;
use Ocpi\Support\Enums\AuthMethod;
use Ocpi\Support\Factories\PriceFactory;

class CdrFactory
{

    /**
     * @param LengthAwarePaginator $collection
     * @return CdrCollection
     */
    public static function fromCollection(LengthAwarePaginator $collection): CdrCollection
    {
        $cdrs = new CdrCollection(
            $collection->currentPage(),
            $collection->perPage(),
            $collection->lastPage(),
            $collection->total(),
        );
        foreach ($collection as $cdr) {
            $cdrs->append(self::fromModel($cdr));
        }
        return $cdrs;
    }

    /**
     * @param \Ocpi\Models\Cdrs\Cdr $model
     * @return Cdr
     */
    public static function fromModel(\Ocpi\Models\Cdrs\Cdr $model): Cdr
    {
        $tariffs = $model->session?->connector->tariffs;
        $tariffCollection = null;
        if (null !== $tariffs) {
            $tariffCollection = TariffFactory::fromCollection($tariffs);
        }
        return new Cdr(
            $model->id,
            $model->party_role_id,
            $model->cdr_id,
            self::createDetailsFromArray($model->object, $tariffCollection),
            $model->location_id,
            $model->location_evse_id,
            $model->session_id,
            $model->external_url
        );
    }

    /**
     * @param array $data
     * @param TariffCollection|null $tariffCollection
     * @return CdrDetails
     */
    public static function createDetailsFromArray(array $data, ?TariffCollection $tariffCollection = null): CdrDetails
    {
        return new CdrDetails(
            $data['country_code'],
            $data['party_id'],
            $data['id'],
            Carbon::createFromTimeString($data['start_date_time']),
            Carbon::createFromTimeString($data['end_date_time']),
            $data['session_id'],
            CdrTokenFactory::fromArray($data['cdr_token']),
            AuthMethod::tryFrom($data['auth_method']),
            $data['authorization_reference'] ?? null,
            CdrLocationFactory::fromArray($data['cdr_location']),
            $data['meter_id'] ?? null,
            $data['currency'],
            $tariffCollection,
            ChargingPeriodFactory::collectionFromArray($data['charging_periods']),
            SignedDataFactory::fromArray($data['signed_data']),
            PriceFactory::fromArray($data['total_cost']),
            PriceFactory::fromArray($data['total_fixed_cost']),
            $data['total_energy'],
            PriceFactory::fromArray($data['total_energy_cost']),
            $data['total_time'],
            PriceFactory::fromArray($data['total_time_cost']),
            $data['total_parking_time'] ?? 0,
            PriceFactory::fromArray($data['total_parking_cost']),
            PriceFactory::fromArray($data['total_reservation_cost']),
            $data['remark'] ?? null,
            $data['invoice_reference_id'] ?? null,
            $data['credit'] ?? false,
            $data['credit_reference_id'] ?? null,
            $data['home_charging_compensation'] ?? null,
            Carbon::createFromTimeString($data['last_updated'])
        );
    }
}