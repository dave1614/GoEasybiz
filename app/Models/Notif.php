<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use HasFactory;

    protected $table = "notif";

    protected $fillable = [
        'sender',
        'receiver',
        'title',
        'message',
        'date_sent',
        'time_sent',
        'type',
    ];
}
