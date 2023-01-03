<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    use HasFactory;
    protected $table = "withdrawal_request";

    protected $fillable = [
        'user_id',
        'amount',
        'bank_name',
        'real_bank_name',
        'account_number',
        'account_name',
        'date',
        'time',
        'debited',
        'debited_date_time',
        'dismissed',
        'dismissed_date_time',
    ];
}
