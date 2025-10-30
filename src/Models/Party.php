<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ocpi\Support\Enums\Role;
use Ocpi\Support\Helpers\Base64Helper;
use Ocpi\Support\Models\Model;

/**
 * @property int $id
 * @property string|null $server_token
 * @property string $client_token
 * @property string|null $encoded_client_token
 * @property string|null $encoded_server_token
 * @property string $code
 * @property string|null $version
 * @property string|null $version_url
 * @property bool $registered
 * @property Collection|PartyRole[] $roles
 * @property PartyRole|null $role_cpo
 * @property array|null $endpoints
 * @property int|null $parent_id
 * @property Party|null $parent
 * @property Collection|Party[] $children
 * @property Collection|PartyToken[] $tokens
 */
class Party extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'url',
        'version',
        'version_url',
        'endpoints',
        'parent_id',
    ];

    /**
     * @param string $token
     *
     * @return string
     * @todo move to helper or static factory
     */
    public static function encodeToken(string $token): string
    {
        return base64_encode($token);
    }

    /**
     * @param string $token
     * @param Party|null $party
     *
     * @return false|string
     * @todo move to helper or static factory
     */
    public static function decodeToken(string $token, ?Party $party = null): false|string
    {
        if ($party && version_compare($party->version, '2.2', '<')) {
            return $token;
        }

        if (true === Base64Helper::isBase64Encoded($token)) {
            return base64_decode($token, true);
        }
        return $token;
    }

    /***
     * Scopes.
     ***/

    public function scopeRegistered(Builder $query, bool $registered = true): void
    {
        $query->where('registered', $registered);
    }

    /***
     * Relations.
     ***/

    public function roles(): HasMany
    {
        return $this->hasMany(PartyRole::class);
    }

    public function role_cpo(): HasOne
    {
        return $this->hasOne(PartyRole::class, 'party_id', 'id')->where('role', Role::CPO->value);
    }


    public function tokens(): HasMany
    {
        return $this->hasMany(PartyToken::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'parent_id');
    }

    public function children(): hasMany
    {
        return $this->hasMany(Party::class, 'parent_id');
    }

    /***
     * Methods.
     ***/

    public function generateToken(): string
    {
        return $this->code . '_' . Str::uuid();
    }

    protected function casts(): array
    {
        return [
            'endpoints' => AsArrayObject::class,
            'registered' => 'boolean',
        ];
    }

    /***
     * Computed Attributes.
     ***/

    protected function encodedClientToken(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => version_compare($this->version, '2.2', '>=')
                ? self::encodeToken($attributes['client_token'])
                : $attributes['client_token'],
        );
    }

    protected function encodedServerToken(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => version_compare($this->version, '2.2', '>=')
                ? self::encodeToken($attributes['server_token'])
                : $attributes['server_token'],
        );
    }
}
