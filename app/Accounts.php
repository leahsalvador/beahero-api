<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Favorites;
class Accounts extends Model
{
    //
    // not fillable column
    protected $guarded = ['id'];
    protected $hidden = [
        'password'
    ];
    public function favorite()
    {
        return $this->hasMany(Favorites::class, 'account_id');
    }
}
