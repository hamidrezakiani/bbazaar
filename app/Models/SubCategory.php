<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SubCategory extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title', 'image', 'status', 'featured', 'admin_id', 'category_id', 'slug', 'meta_title', 'meta_description'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id')
            ->select(['id', 'title', 'slug']);
    }

    public function categ():BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id', 'id');
    }
    public function language(): HasMany
    {
        return $this->hasMany(SubCategoryLang::class, 'sub_category_id', 'id');
    }
}
