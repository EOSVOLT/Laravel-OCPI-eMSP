<?php

namespace Ocpi\Modules\Tariffs\Repositories;

use Illuminate\Http\Request;
use Ocpi\Models\Tariffs\Tariff;
use Ocpi\Models\Tariffs\TariffElement;
use Ocpi\Models\Tariffs\TariffElementPriceComponents;
use Ocpi\Models\Tariffs\TariffPriceComponents;
use Ocpi\Models\Tariffs\TariffRestriction;
use Ocpi\Modules\Tariffs\Factories\TariffFactory;

class TariffRepository
{
    /**
     * @param int $partyId
     * @param array $data
     * @return \Ocpi\Modules\Tariffs\Objects\Tariff
     */
    public function createOrUpdateFromArray(int $partyId, array $data): \Ocpi\Modules\Tariffs\Objects\Tariff
    {
        $minPrice = $data['min_price'] ?? null;
        $maxPrice = $data['max_price'] ?? null;
        $tariff = Tariff::withTrashed()->updateOrCreate(
            [
                'party_id' => $partyId,
                'external_id' => $data['id'],
            ],
            [
                'currency' => $data['currency'],
                'type' => $data['type'] ?? null,
                'tariff_alt_text' => $data['tariff_alt_text'] ?? null,
                'tariff_alt_url' => $data['tariff_alt_url'] ?? null,
                'min_price_excl_vat' => $minPrice['excl_vat'] ?? null,
                'min_price_incl_vat' => $minPrice['incl_vat'] ?? null,
                'max_price_excl_vat' => $maxPrice['excl_vat'] ?? null,
                'max_price_incl_vat' => $maxPrice['incl_vat'] ?? null,
            ]
        );
        if (true === $tariff->trashed()){
            $tariff->restore();
        }
        $this->clearElementsForTariff($tariff);
        $this->createElementsForTariff($tariff, $data['elements'] ?? []);
        return TariffFactory::fromModel($tariff);
    }

    /**
     * @param Tariff $tariff
     * @param array $elements
     * @return void
     */
    private function createElementsForTariff(Tariff $tariff, array $elements): void
    {
        foreach ($elements as $element) {
            $restrictionModel = $this->createRestrictionsFromArray($element);
            $elementModel = TariffElement::query()->create([
                'tariff_id' => $tariff->id,
                'tariff_restriction_id' => $restrictionModel?->id,
            ]);

            $this->createPriceComponents($elementModel, $element['price_components']);
        }
    }

    /**
     * @param Tariff $tariff
     *
     * @return void
     */
    private function clearElementsForTariff(Tariff $tariff): void
    {
        $elementIds = TariffElement::query()
            ->where('tariff_id', $tariff->id)
            ->pluck('id');

        if ($elementIds->isEmpty()) {
            return;
        }

        TariffElementPriceComponents::query()
            ->whereIn('tariff_element_id', $elementIds)
            ->delete();

        TariffElement::query()
            ->whereIn('id', $elementIds)
            ->delete();
    }

    /**
     * @param TariffElement $tariffElement
     * @param array $priceComponents
     * @return void
     */
    private function createPriceComponents(TariffElement $tariffElement, array $priceComponents): void
    {
        foreach ($priceComponents as $priceComponent) {
            $priceComponent = TariffPriceComponents::query()->firstOrCreate([
                'dimension_type' => $priceComponent['type'],
                'price' => $priceComponent['price'],
                'vat' => $priceComponent['vat'],
                'step_size' => $priceComponent['step_size'],
            ]);
            TariffElementPriceComponents::query()->firstOrCreate([
                'tariff_element_id' => $tariffElement->id,
                'tariff_price_component_id' => $priceComponent->id,
            ]);
        }
    }

    /**
     * @param array $element
     *
     * @return TariffRestriction|null
     */
    private function createRestrictionsFromArray(array $element): ?TariffRestriction
    {
        if (empty($element['restriction'])) {
            return null;
        }
        return TariffRestriction::query()->firstOrCreate([
            'start_time' => $element['restriction']['start_time'] ?? null,
            'end_time' => $element['restriction']['end_time'] ?? null,
            'start_date' => $element['restriction']['start_date'] ?? null,
            'end_date' => $element['restriction']['end_date'] ?? null,
            'min_kwh' => $element['restriction']['min_kwh'] ?? null,
            'max_kwh' => $element['restriction']['max_kwh'] ?? null,
            'min_current' => $element['restriction']['min_current'] ?? null,
            'max_current' => $element['restriction']['max_current'] ?? null,
            'min_power' => $element['restriction']['min_power'] ?? null,
            'max_power' => $element['restriction']['max_power'] ?? null,
            'min_duration' => $element['restriction']['min_duration'] ?? null,
            'max_duration' => $element['restriction']['max_duration'] ?? null,
            'day_of_week' => $element['restriction']['day_of_week'] ?? null,
            'reservation' => $element['restriction']['reservation'] ?? null,
        ]);
    }
}