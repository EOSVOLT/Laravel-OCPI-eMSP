<?php

namespace Ocpi\Modules\Tariffs\Repositories;

use Illuminate\Http\Request;
use Ocpi\Models\Tariff\Tariff;
use Ocpi\Models\Tariff\TariffElement;
use Ocpi\Models\Tariff\TariffElementPriceComponents;
use Ocpi\Models\Tariff\TariffPriceComponents;
use Ocpi\Modules\Tariffs\Factories\TariffFactory;

class TariffRepository
{
    public function createOrUpdateFromArray(int $partyId, array $data): \Ocpi\Modules\Tariffs\Objects\Tariff
    {
        $minPrice = $data['min_price'] ?? null;
        $maxPrice = $data['max_price'] ?? null;
        $elements = $data['elements'] ?? [];
        $tariffModel = Tariff::query()->updateOrCreate(
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
        foreach ($elements as $element) {
//            $restrictionModel = $this->createRestrictionsFromSpark($element);
            $elementModel = TariffElement::query()->create([
                'tariff_id' => $tariffModel->id,
                'tariff_restriction_id' => null,
            ]);
            $this->clearElementsForTariff($tariffModel);
            $this->createPriceComponents($elementModel, $element['price_components']);
        }
        return TariffFactory::fromModel($tariffModel);
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

    private function createPriceComponents(TariffElement $elementModel, array $priceComponents): void
    {
        foreach ($priceComponents as $priceComponent) {
            $priceComponent = TariffPriceComponents::query()->firstOrCreate([
                'dimension_type' => $priceComponent['type'],
                'price' => $priceComponent['price'],
                'vat' => $priceComponent['vat'],
                'step_size' => $priceComponent['step_size'],
            ]);
            TariffElementPriceComponents::query()->firstOrCreate([
                'tariff_element_id' => $elementModel->id,
                'tariff_price_component_id' => $priceComponent->id,
            ]);
        }
    }
}