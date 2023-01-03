<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalHistory extends Model
{
    use HasFactory;

    protected $table = "withdrawal_history";

    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'time',
    ];
}
