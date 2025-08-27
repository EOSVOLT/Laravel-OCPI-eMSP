<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Ocpi\Support\Models\Model;

/**
 * @property string $server_token
 * @property string $client_token
 * @property string $encoded_client_token
 * @property string $encoded_server_token
 * @property string $code
 * @property string $url
 * @property string $name
 * @property string $version
 * @property string $version_url
 * @property bool $registered
 * @property Collection|PartyRole[] $roles
 */
class Party extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'server_token',
        'url',
        'version',
        'version_url',
        'endpoints',
        'client_token',
        'registered',
    ];

    public static function encodeToken(string $token): string
    {
        return base64_encode($token);
    }

    public static function decodeToken(string $token, ?Party $party = null): false|string
    {
        if ($party && version_compare($party->version, '2.2', '<')) {
            return $token;
        }

        return base64_decode($token, true);
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
