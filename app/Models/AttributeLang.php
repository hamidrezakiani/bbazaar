<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeLang extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id', 'title', 'lang'
    ];

    public function values()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id');
    }


}
