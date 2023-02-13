<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Categories;
use App\Http\Models\Users;

class Products extends Model
{
    // 
    protected $fillable = [
        "categoryId",
        "merchantId",
        "title",
        "description",
        "image",
        "stocks",
        "rating",
        "meta",
        "price",
        "cost",
        "status",
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'categoryId');
    }

    public function merchant()
    {
        return $this->belongsTo(Users::class, 'merchantId');
    }
}
