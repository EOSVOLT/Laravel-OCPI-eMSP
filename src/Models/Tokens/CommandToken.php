<?php

namespace Ocpi\Models\Tokens;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ocpi\Models\PartyRole;
use Ocpi\Modules\Commands\Enums\ProfileType;
use Ocpi\Modules\Commands\Enums\WhitelistType;
use Ocpi\Modules\Locations\Enums\TokenType;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property string $uid
 * @property TokenType $type
 * @property null|string $visual_number
 * @property null|string $group_id
 * @property bool $valid
 * @property WhitelistType $whitelist_type
 * @property string $language
 * @property ProfileType $default_profile_type
 * @property array $energy_contract
 * @property string $issuer
 * @property string $contract_id
 * @property PartyRole $party_role
 * @property string $updated_at
 * @property int $party_role_id
 */
class CommandToken extends Model
{
    protected $fillable = [
        'party_role_id',
        'uid',
        'type',
        'visual_number',
        'group_id',
        'issuer',
        'contract_id',
        'valid',
        'whitelist_type',
        'language',
        'default_profile_type',
        'energy_contract',
    ];

    protected function casts(): array
    {
        return [
            'default_profile_type' => ProfileType::class,
            'type' => TokenType::class,
            'whitelist_type' => WhitelistType::class,
            'energy_contract' => 'array',
        ];
    }

    public function party_role(): BelongsTo
    {
        return $this->belongsTo(PartyRole::class, 'party_role_id', 'id');
    }
}
