<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Users;
use App\Http\Models\Products;
use App\Http\Models\OrderProducts;

class Transactions extends Model
{
    // 
    protected $fillable = [
        "customerId",
        "riderId",
        "merchantId",
        "productId",
        "pickUpDestination",
        "dropOffDestination",
        "notes",
        "serviceFee",
        "status"
    ];

    protected $casts = [
        "pickUpDestination" => 'array',
        "dropOffDestination" => 'array',
        "serviceFee" => 'array'
    ];

    
    public function customer()
    {
        return $this->belongsTo(Users::class, 'customerId');
    }

    public function merchant()
    {
        return $this->belongsTo(Users::class, 'merchantId');
    }

    public function rider()
    {
        return $this->belongsTo(Users::class, 'riderId');
    }

    public function products()
    {
        return $this->hasMany(OrderProducts::class, 'transactionId');
    }
}
