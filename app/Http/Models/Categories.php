<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Users;

class Categories extends Model
{
    // 
    protected $fillable = [
        'merchantId',
        'userId',
        'title',
        'description',
        'image',
        'status'
    ];

    public function tenant()
    {
        return $this->belongsTo(Users::class, 'tenantId');
    }
}
