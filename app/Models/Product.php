<?php

namespace App\Models;

use Config;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model implements HasMedia, Auditable
{
    use HasFactory;
    use InteractsWithMedia;
    use \OwenIt\Auditing\Auditable;
    
    // public const UPDATED_AT = null;

    protected $fillable  = [
        'id', 'title', 'purchased', 'selling', 'offered', 'image', 'unit', 'video', 'video_thumb', 'badge',
        'status', 'admin_id', 'subcategory_id', 'category_id', 'brand_id', 'warranty', 'refundable',
        'description', 'overview', 'tags', 'tax_rule_id', 'shipping_rule_id', 'meta_title', 'meta_description',
        'review_count', 'rating', 'bundle_deal_id', 'slug','sku'
    ];

    protected $hidden = [];


    public function flash_sale_product(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class, 'product_id', 'id')
            ->with('flash_sale');
    }


    public function tax_rules(): BelongsTo
    {
        return $this->belongsTo(TaxRules::class, 'tax_rule_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }
//    public function category(): HasOne
//    {
//        return $this->hasOne(Category::class, 'id', 'category_id')->select(['id', 'title', 'slug']);
//    }

    public function product_categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'product_id', 'id')
            ->orderBy('primary', 'DESC');
    }


    public function shipping_rule(): BelongsTo
    {
        return $this->belongsTo(ShippingRule::class, 'shipping_rule_id');
    }


//    public function product_collections(): BelongsToMany
//    {
//        return $this->belongsToMany(CollectionWithProduct::class, 'product_id', 'id')
//            ->select(['id', 'product_id', 'product_collection_id']);
//    }

//    public function product_collections(): BelongsToMany
//    {
//        return $this->belongsToMany(ProductCollection::class, CollectionWithProduct::class, 'product_id', 'product_collection_id', 'id', 'id');
//    }

    public function product_collections(): BelongsToMany
    {
        return $this->belongsToMany(ProductCollection::class, CollectionWithProduct::class, 'product_id', 'product_collection_id', 'id', 'id');
    }

    public function product_inventories(): HasMany
    {
        return $this->hasMany(UpdatedInventory::class, 'product_id', 'id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(UpdatedInventory::class, 'product_id', 'id');
    }

    public function product_images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function product_image_names(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function store()
    {
        return $this->hasOne(Store::class, 'admin_id', 'admin_id');
    }

    public function bundle_deal(): BelongsTo
    {
        return $this->belongsTo(BundleDeal::class, 'bundle_deal_id')
            ->select(['id', 'buy', 'free', 'title']);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function language(): HasMany
    {
        return $this->hasMany(ProductLang::class, 'product_id', 'id');
    }

    public function qty(): HasMany
    {
        return $this->hasMany(UpdatedInventory::class, 'product_id', 'id');
    }
    public function qty1(): HasOne {
        return $this->hasOne(UpdatedInventory::class, 'product_id', 'id');

    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    public function current_categories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'category_id')
            ->offset(0)
            ->limit(10)
            ->select('id', 'category_id', 'title', 'slug')
            ->where('status', Config::get('constants.status.PUBLIC'));
    }
}
