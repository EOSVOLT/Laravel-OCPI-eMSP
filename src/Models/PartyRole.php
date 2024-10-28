<?php

namespace Ocpi\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ocpi\Support\Models\Model;

class PartyRole extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'role',
        'country_code',
        'business_details',
    ];

    protected function casts(): array
    {
        return [
            'business_details' => AsArrayObject::class,
        ];
    }

    /***
     * Relations.
     ***/

    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class);
    }
}
