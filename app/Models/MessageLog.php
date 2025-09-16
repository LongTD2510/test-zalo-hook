<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    protected $fillable = [
        'type',
        'phone',
        'user_id',
        'template_id',
        'template_name',
        'tracking_id',
        'status',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'request_payload'   => 'array',
        'response_payload'  => 'array',
    ];
}