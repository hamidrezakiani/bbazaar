<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HeaderLink extends Model
{
    use HasFactory;


    protected $fillable = [
        'title', 'url', 'type', 'admin_id'
    ];

    protected $hidden = [
        'admin_id'
    ];

    public function language():HasMany {
        return $this->hasMany(HeaderLinkLang::class,'header_link_id');
    }


}


