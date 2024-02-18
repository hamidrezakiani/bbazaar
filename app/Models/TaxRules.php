<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRules extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'type', 'admin_id', 'price'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function language():HasMany {
        return $this->hasMany(TaxRuleLang::class,'tax_rule_id');
    }
}
