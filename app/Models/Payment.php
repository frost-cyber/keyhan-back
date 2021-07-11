<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	const STATUS_PAYED = 'پرداخت شده';
	const STATUS_PAY_CANCELED = 'پرداخت لغو شده';
	const STATUS_PAY_PENDING = 'در انتظار پرداخت';

    use HasFactory;
    protected $fillable = ['gateway' , 'status' , 'data','amount'];
    protected $casts = [
        'data' => 'json'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
