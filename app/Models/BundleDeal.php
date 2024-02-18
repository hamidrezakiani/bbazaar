<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BundleDeal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'free', 'admin_id', 'buy'
    ];

    protected $hidden = [
        'admin_id'
    ];


    public function language(): HasMany
    {
        return $this->hasMany(BundleDealLang::class, 'bundle_deal_id');
    }
}

