<?php

namespace Ocpi\Modules\Sessions\Factories;

use Illuminate\Support\Carbon;
use Ocpi\Models\Sessions\Session;
use Ocpi\Modules\Cdrs\Factories\CdrTokenFactory;
use Ocpi\Modules\Cdrs\Factories\ChargingPeriodFactory;
use Ocpi\Modules\Sessions\Objects\SessionDetails;
use Ocpi\Support\Enums\AuthMethod;
use Ocpi\Support\Enums\SessionStatus;
use Ocpi\Support\Factories\PriceFactory;

class SessionFactory
{
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
        return new SessionDetails(
            $data['id'],
            $data['country_code'],
            $data['party_id'],
            Carbon::createFromTimeString($data['start_date_time ']),
            Carbon::createFromTimeString($data['end_date_time ']),
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