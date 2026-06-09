<?php

namespace App\Models\Concerns;

use App\Support\SensitiveData;

trait MasksSensitiveAttributes
{
    public function toArray(): array
    {
        $array = parent::toArray();

        foreach ($this->sensitiveMasks() as $attribute => $mask) {
            if (!array_key_exists($attribute, $array)) {
                continue;
            }

            $array[$attribute] = match ($mask) {
                'phone' => SensitiveData::maskPhone($this->getAttribute($attribute)),
                'bank' => SensitiveData::maskBankAccount($this->getAttribute($attribute)),
                'national_id' => SensitiveData::maskNationalId($this->getAttribute($attribute)),
                default => SensitiveData::mask($this->getAttribute($attribute)),
            };
        }

        return $array;
    }

    protected function sensitiveMasks(): array
    {
        return property_exists($this, 'sensitiveMaskedAttributes')
            ? $this->sensitiveMaskedAttributes
            : [];
    }
}
