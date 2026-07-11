<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    protected $fillable = [
        'ministry_id',
        'title',
        'slug',
        'description',
        'required_documents',
        'fee',
        'processing_days',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'fee' => 'decimal:2',
        'processing_days' => 'integer',
    ];

    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}