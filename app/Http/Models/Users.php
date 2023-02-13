<?php

    namespace App\Http\Models;
    use App\Http\Models\RiderWallets;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Tymon\JWTAuth\Contracts\JWTSubject;

    class Users extends Authenticatable implements JWTSubject
    {
        use Notifiable;

        /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
        protected $fillable = [
            'facebookId',
            'name', 
            'email', 
            'lastName', 
            'firstName', 
            'middleName', 
            'phoneNumber',
            'password',
            'type',
            'subscriptionPlan',
            'address',
            'latitude',
            'longitude',
            'isBusy',
            'image',
            'businessHours',
            'businessType',
            'isViewAds',
            'status'
        ];

        protected $casts = [
            "businessHours" => 'array'
        ];

        /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
        protected $hidden = [
            'password', 'remember_token',
        ];

        public function getJWTIdentifier()
        {
            return $this->getKey();
        }
        public function getJWTCustomClaims()
        {
            return [];
        }

        public function wallet()
        {
            return $this->belongsTo(RiderWallets::class, 'id', 'riderId');
        }
    
    }