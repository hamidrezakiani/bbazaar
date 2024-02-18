<?php

namespace App\Models;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GlobalLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code' => 'string',
        'name' => 'string',
        'script' => 'string',
        'native' => 'string',
    ];
}
