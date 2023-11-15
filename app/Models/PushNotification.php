<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'multicast_id',
        'success',
        'failure',
        'canonical_ids',
        'message_id',
        'device_token',
        'product_id'
    ];
}
