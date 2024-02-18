<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'site_name', 'meta_title', 'meta_description', 'email_logo', 'site_url',
        'header_logo', 'footer_logo', 'copyright_text', 'admin_id',
        'primary_color', 'primary_hover_color', 'styling'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function language():HasMany {
        return $this->hasMany(SiteSettingLang::class,'site_setting_id');
    }
}
