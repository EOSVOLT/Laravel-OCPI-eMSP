<?php

namespace Ocpi\Models\Tariff;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Ocpi\Models\Party;

/**
 * @property int $id
 * @property string $external_id
 * @property string $currency
 * @property string $type
 * @property array $tariff_alt_text
 * @property string $tariff_alt_url
 * @property ?float $min_price_excl_vat
 * @property ?float $min_price_incl_vat
 * @property ?float $max_price_excl_vat
 * @property ?float $max_price_incl_vat
 * @property Party $party
 * @property TariffElement[]|Collection $elements
 * @property Carbon $updated_at
 */
class Tariff extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tariff_alt_text' => 'json',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('ocpi.database.table.prefix') . 'tariffs';
    }


    /**
     * @return BelongsTo
     */
    public function party(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'party_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function elements(): HasMany
    {
        return $this->hasMany(TariffElement::class, 'tariff_id', 'id');
    }
}
