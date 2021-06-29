<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['gateway' , 'status' , 'data','amount'];
    protected $casts = [
        'data' => 'json'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
