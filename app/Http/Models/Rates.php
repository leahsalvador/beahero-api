<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    // 
    protected $fillable = [
        "type",
        "amount",
        "status",
    ];
}