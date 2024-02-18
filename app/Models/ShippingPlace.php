<?php

namespace App\Models;

use Squire\Models\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'price',
        'state',
        'country',
        'admin_id' ,
        'day_needed',
        'shipping_rule_id',

        'pickup_point',
        'pickup_price',
        'pickup_phone',
        'pickup_address_line_1',
        'pickup_address_line_2',
        'pickup_zip',
        'pickup_state',
        'pickup_city',
        'pickup_country'
    ];

    protected $hidden = [
        'admin_id',  'created_at',  'updated_at'
    ];

    public function shipping_rule()
    {
        return $this->hasOne(ShippingRule::class, 'id', 'shipping_rule_id');
    }

    public function stateCode(): HasMany
    {
        return $this->hasMany(Region::class, 'state', 'id');
    }

}
