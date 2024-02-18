<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingRule extends Model
{
    use HasFactory;

    protected $casts = [
        'single_price' => 'integer'
    ];

    protected $fillable = [
        'id', 'title', 'admin_id', 'single_price'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function shipping_places()
    {
        return $this->hasMany(ShippingPlace::class, 'shipping_rule_id', 'id');
    }

    public function language():HasMany {
        return $this->hasMany(ShippingRuleLang::class,'shipping_rule_id');
    }

    public function getCountries($parent = null): array
    {
        $countries = $this->getCountryFile();
        $jsonContents = file_get_contents($countries);
        $data = json_decode($jsonContents, true);
        $filteredCountries = [];
        foreach ($data as $countryCode => $countryData) {
            if ($parent === null || (isset($countryData['parent']) && $countryData['parent'] === $parent)) {
                $filteredCountries[$countryCode] = $countryData['name'];
            }
        }

        return $filteredCountries;
    }

    public function getStates($countryCode = null){
        $countries = $this->getCountryFile();
        $jsonContents = file_get_contents($countries);
        $data = json_decode($jsonContents, true);

        if (isset($data[$countryCode]['states'])) {
            $states = $data[$countryCode]['states'];
            $stateCodes = array_keys($states);
            $stateNames = array_values($states);

            return array_combine($stateCodes, $stateNames);
        }

        return null;

//        if (isset($data[$code]['states'])) {
//            return $data[$code]['states'];
//        }
//
//        return null;
    }

    public function getCountryFile(): string
    {
         return storage_path('resources/countriesbackend.json');
    }
}
