<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = ['status' , 'tracking_code' , 'shipments_date','address_id'];
    protected $casts = [
        'shipments_date' => 'timestamp'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function address(){
        return $this->belongsTo(Address::class);
    }
}
