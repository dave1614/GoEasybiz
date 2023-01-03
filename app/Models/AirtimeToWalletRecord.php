<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirtimeToWalletRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount_requested',
        'amount_credited',
        'admin_amount',
        'date',
    ];
}
