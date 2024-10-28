<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Ocpi\Support\Models\Model;

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
            get: fn (mixed $value, array $attributes) => self::encodeToken($attributes['client_token']),
        );
    }

    protected function encodedServerToken(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => self::encodeToken($attributes['server_token']),
        );
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
        return $this->code.'_'.Str::uuid();
    }

    public static function encodeToken(string $token): string
    {
        return base64_encode($token);
    }

    public static function decodeToken(string $token): false|string
    {
        return base64_decode($token, true);
    }
}
