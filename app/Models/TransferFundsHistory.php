<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFundsHistory extends Model
{
    use HasFactory;

    protected $table = "transfer_funds_history";

    protected $fillable = [
        'sender',
        'recepient',
        'amount',
        'date',
        'time',
    ];
}
