<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}