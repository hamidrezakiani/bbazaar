<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;


    protected $casts = [
        'predefined' => 'integer',
        'default' => 'integer'
    ];






    protected $fillable = [
        'name', 'code', 'direction', 'status', 'default', 'predefined','admin_id'
    ];

    protected $hidden = [
        'admin_id'
    ];



    public function save(array $options = [])
    {
        if ($this->isDirty('default') && $this->attributes['default'] == 0) {
            // Set the default value to 0 for all other records
            Language::first()->update(['default' => 1]);
        }

        return parent::save($options);
    }
}
