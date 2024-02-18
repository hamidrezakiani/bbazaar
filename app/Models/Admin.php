<?php

namespace App\Models;

use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;


    protected $casts = [
        'remember_token' => 'integer',
        'verified' => 'integer',
        'active' => 'integer',
        'viewed' => 'integer'
    ];

    protected $fillable = [
        'name',
        'username',
        'email',
        'commission',
        'password',
        'code',
        'verified',
        'viewed',
        'active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === "admin") {
            return $this->type === 'admin';
        }
        if ($panel->getId() === "seller") {
            return $this->type === 'seller';
        }

        return false;
    }

    public function language(): HasMany
    {
        return $this->hasMany(Language::class, 'admin_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'admin_id');
    }

}
