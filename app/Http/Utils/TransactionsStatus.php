<?php

namespace App\Http\Utils;

class TransactionsStatus
{
    const IDLE = 0;
    const CONFIRMED = 1;
    const FOR_PICK_UP = 2;
    const FOR_DROP_OFF = 3;
    const DROPPED_OFF_LOCATION = 4;
    const DELIVERED = 5;
    const CANCELLED = 6;
    const REJECTED = 7;
}

// This was based upon the discussion with leah.

// 1 - finding rider, 
// 2 - rider confirmed
// 3 - rider on the way to pick up item / store , 
// 4 - rider on the way to you ,  
// 5 - rider arrive in drop off point,
// 6 - delivery complete
// 7 - cancelled
// 8 - rejected