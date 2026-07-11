<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    protected $fillable = [
    'application_id',
    'label',
    'file_path',
    'status',
    'note',
    'original_name',
    'mime_type',
    'size',
];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}