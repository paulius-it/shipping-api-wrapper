<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $table = 'shipping';
    protected $fillable = [
        'tracking_number',
        'provider_id',
        'user_id',
        'sender_address',
        'receiver_address',
        'phone',
        'receiver_email',

        'item',
        'quantity',
    ];

    // Constants for shipping operations
    const SHIPPING_CREATED = 3;
    const SHIPPING_UPDATED = 4;
    const SHIPPING_REMOVED = 5;
    const COURIER_CALLED = 6;
    const LABEL_DOWNLOAD = 7;
}
