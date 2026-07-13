<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
	
	public function users(): HasMany
{
    return $this->hasMany(User::class);
}

public function agents(): HasMany
{
    return $this->hasMany(User::class)
        ->where('role', User::ROLE_AGENT);
}

public function responsables(): HasMany
{
    return $this->hasMany(User::class)
        ->where('role', User::ROLE_RESPONSABLE);
}
}