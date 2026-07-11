<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model { protected $fillable=['application_id','reference','provider','amount','status','paid_at']; protected $casts=['amount'=>'decimal:2','paid_at'=>'datetime']; public function application(){return $this->belongsTo(Application::class);} }
