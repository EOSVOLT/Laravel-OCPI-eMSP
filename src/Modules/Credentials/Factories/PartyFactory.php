<?php

namespace Ocpi\Modules\Credentials\Factories;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Ocpi\Models\PartyToken;
use Ocpi\Modules\Credentials\Object\Party;
use Ocpi\Modules\Credentials\Object\PartyCollection;
use Ocpi\Modules\Credentials\Object\PartyRoleCollection;

class PartyFactory
{
    /**
     * @param \Ocpi\Models\Party $model
     * @param PartyToken|null $partyToken
     * @return Party
     */
    public static function fromModel(\Ocpi\Models\Party $model, ?PartyToken $partyToken = null): Party
    {
        if (null === $partyToken) {
            $partyToken = $model->tokens->first();
        }

        $roles = self::roleRelation($model);
        return new Party(
            $model->id,
            $model->code,
            $partyToken->token,
            $model->url,
            $model->version,
            $model->version_url,
            $model->endpoints ?? [],
            $partyToken->registered,
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