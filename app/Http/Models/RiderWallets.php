<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RiderWallets extends Model
{
    // 
    protected $fillable = [
        "riderId",
        "amount",
        // "status",
    ];
}