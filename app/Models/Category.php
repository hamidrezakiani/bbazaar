<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Config;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;


    protected $fillable = [
        'title', 'image', 'status', 'admin_id', 'slug', 'meta_title', 'meta_description'
    ];

    protected $hidden = [
        'admin_id'
    ];

    // public function parent_data(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class, 'parent');
    // }

    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class, 'parent')->where('parent',null);
    // }

    public function single_parent(): HasOne
    {
        return $this->hasOne(Category::class, 'parent');
    }

    public function parentRecursive()
    {
        return $this->parent_data()->with('parentRecursive', 'languages');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(CategoryLang::class, 'category_id', 'id');
    }

    public function translations(): HasOne
    {
        return $this->hasOne(CategoryLang::class, 'category_id');
    }

    // public function children()
    // {
    //     return $this->hasMany(Category::class, 'parent');
    // }

    // public function child()
    // {
    //     return $this->hasMany(Category::class, 'parent')->with('child');
    // }

    // public function in_footer_child()
    // {
    //     return $this->hasMany(Category::class, 'parent')
    //         ->with('in_footer_child')
    //         ->where('status', Config::get('constants.status.PUBLIC'))
    //         ->where('in_footer', Config::get('constants.status.PUBLIC'))
    //         ->select(['id', 'title', 'slug', 'parent']);
    // }

    public function public_sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id')
            ->where('status', Config::get('constants.status.PUBLIC'))
            ->select('id','title', 'slug', 'category_id');
    }

    public function sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

    // public function determineOrderColumnName(): string
    // {
    //     return "order";
    // }

    //  public function determineParentColumnName(): string
    //  {
    //      return "parent";
    //  }

    // public function determineTitleColumnName(): string
    // {
    //     return 'title';
    // }

    //  public static function defaultParentKey()
    //  {
    //      return null;
    //  }

//     public static function defaultChildrenKeyName(): string
//     {
//         return "children";
//     }


}
