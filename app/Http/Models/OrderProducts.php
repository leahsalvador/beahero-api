<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Categories;
use App\Http\Models\Users;
use App\Http\Models\Products;

class OrderProducts extends Model
{
    // 
    protected $fillable = [
        "transactionId",
        "categoryId",
        "cost",
        "price",
        "productId",
        "quantity"
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categoryId');
    }

    public function merchant()
    {
        return $this->belongsTo(Users::class, 'userId');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'productId');
    }
}
