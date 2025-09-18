<?php

namespace Ocpi\Modules\Credentials\Factories;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ocpi\Modules\Credentials\Object\Party;
use Ocpi\Modules\Credentials\Object\PartyRoleCollection;

class PartyFactory
{
    /**
     * @param \Ocpi\Models\Party $model
     *
     * @return Party
     */
    public static function fromModel(\Ocpi\Models\Party $model): Party
    {
        $roles = self::roleRelation($model);
        return new Party(
            $model->id,
            $model->name,
            $model->code,
            $model->server_token,
            $model->url,
            $model->version,
            $model->version_url,
            (array)$model->endpoints,
            $model->client_token,
            $model->registered,
            $model->cpo_id,
            $roles,
        );
    }

    private static function roleRelation(\Ocpi\Models\Party $party): ?PartyRoleCollection
    {
        $roleCollection = null;
        if ($party->relationLoaded('roles')) {
            $roleCollection = new PartyRoleCollection();
            foreach ($party->roles as $role) {
                $roleCollection->add(PartyRoleFactory::fromModel($role));
            }
        } elseif ($party->relationLoaded('role_cpo')) {
            $roleCollection = new PartyRoleCollection();
            $roleCollection->add(PartyRoleFactory::fromModel($party->role_cpo));
        }
        return $roleCollection;
    }

    /**
     * @param Collection|LengthAwarePaginator $collection
     * @return PartyCollection
     */
    public static function fromCollection(Collection|LengthAwarePaginator $collection): PartyCollection
    {
        $parties = new PartyCollection(
            $collection->currentPage(),
            $collection->perPage(),
            $collection->lastPage(),
            $collection->total()
        );
        foreach ($collection as $party) {
            $parties->append(self::fromModel($party));
        }
        return $parties;
    }
}