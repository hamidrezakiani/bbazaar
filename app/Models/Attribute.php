<?php

namespace App\Models;

use App\Models\AttributeValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'admin_id'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id');
    }

    public function language(): HasMany {
        return $this->hasMany(AttributeLang::class, 'attribute_id');
    }
}
