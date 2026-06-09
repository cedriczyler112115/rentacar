<?php

namespace App\Services;

use App\Models\LibMunicipalityTypePrice;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class MunicipalityTypePricingService
{
    public function getPriceForType(int $municipalityId, int $libTypeId): float
    {
        $row = LibMunicipalityTypePrice::query()
            ->where('lib_municipality_id', $municipalityId)
            ->where('lib_type_id', $libTypeId)
            ->first();

        if ($row) {
            return (float) $row->price;
        }

        throw ValidationException::withMessages([
            'price' => 'No price configured for this municipality and car type.',
        ]);
    }

    public function setPriceForType(int $municipalityId, int $libTypeId, $price): LibMunicipalityTypePrice
    {
        $validator = Validator::make(
            ['price' => $price],
            ['price' => ['required', 'numeric', 'min:0.01']]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return LibMunicipalityTypePrice::updateOrCreate(
            [
                'lib_municipality_id' => $municipalityId,
                'lib_type_id' => $libTypeId,
            ],
            [
                'price' => $validator->validated()['price'],
            ]
        );
    }

    public function deletePriceForType(int $municipalityId, int $libTypeId): int
    {
        return LibMunicipalityTypePrice::query()
            ->where('lib_municipality_id', $municipalityId)
            ->where('lib_type_id', $libTypeId)
            ->delete();
    }
}
