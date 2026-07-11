<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'procedure_id',
        'reference',
        'status',
        'payment_status',
        'priority',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class);
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function workflowLogs()
    {
        return $this->hasMany(WorkflowLog::class);
    }
}