<?php

namespace Ocpi\Modules\Sessions\Factories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Cdrs\Factories\CdrTokenFactory;
use Ocpi\Modules\Cdrs\Factories\ChargingPeriodFactory;
use Ocpi\Modules\Sessions\Objects\SessionCollection;
use Ocpi\Modules\Sessions\Objects\SessionDetails;
use Ocpi\Support\Enums\AuthMethod;
use Ocpi\Support\Enums\SessionStatus;
use Ocpi\Support\Factories\PriceFactory;

class SessionFactory
{
    /**
     * @param Collection|LengthAwarePaginator $collection
     * @return SessionCollection
     */
    public static function fromCollection(Collection|Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator $collection): SessionCollection
    {
        $sessionCollection = new SessionCollection(
            page: $collection->currentPage(),
            perPage: $collection->perPage(),
            totalPages: ($collection->total()/$collection->perPage()),
            totalResults: $collection->total(),
        );
        foreach ($collection as $session) {
            $sessionCollection->append(self::fromModel($session));
        }
        return $sessionCollection;
    }

    /**
     * @param Session $model
     * @return \Ocpi\Modules\Sessions\Objects\Session
     */
    public static function fromModel(Session $model): \Ocpi\Modules\Sessions\Objects\Session
    {
        return new \Ocpi\Modules\Sessions\Objects\Session(
            $model->id,
            $model->party_role_id,
            $model->location_id,
            $model->session_id,
            $model->status,
            self::createDetailsFromArray($model->object)
        );
    }

    /**
     * @param array $data
     * @return SessionDetails
     */
    public static function createDetailsFromArray(array $data): SessionDetails
    {
        $startDate = Carbon::createFromTimeString($data['start_date_time']);
        $endDate = true === isset($data['end_date_time']) ? Carbon::createFromTimeString($data['end_date_time']) : null;
        return new SessionDetails(
            $data['id'],
            $data['country_code'],
            $data['party_id'],
            $startDate,
            $endDate,
            $data['kwh'],
            CdrTokenFactory::fromArray($data['cdr_token']),
            AuthMethod::tryFrom($data['auth_method']),
            $data['authorization_reference'],
            $data['location_id'],
            $data['evse_uid'],
            $data['connector_id'],
            $data['meter_id'],
            $data['currency'],
            ChargingPeriodFactory::collectionFromArray($data['charging_periods']),
            PriceFactory::fromArray($data['total_cost']),
            SessionStatus::tryFrom($data['status']),
            Carbon::createFromTimeString($data['last_updated']),
        );
    }
}