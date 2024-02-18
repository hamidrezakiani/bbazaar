<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'title', 'image', 'status', 'featured', 'admin_id', 'slug'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function language(): HasMany
    {
        return $this->hasMany(BrandLang::class, 'brand_id', 'id');
    }
}
