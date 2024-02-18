<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Store extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $casts = [
        'whatsapp_btn' => 'integer'
    ];

    protected $fillable = [
        'image', 'name', 'slug', 'admin_id', 'meta_title', 'meta_description',
        'whatsapp_btn', 'whatsapp_number', 'whatsapp_default_msg'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function language():HasMany {
        return $this->hasMany(StoreLang::class,'store_id');
    }

    public function user():BelongsTo {
        return $this->belongsTo(Admin::class, 'admin_id','id');
    }

    public function followers():HasMany {
        return $this->hasMany(UserFollowStore::class, 'store_id');
    }

    public function products(): BelongsTo
    {
        return $this->belongsTo(Admin::class,'admin_id');
    }

}
