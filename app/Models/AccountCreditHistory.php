<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountCreditHistory extends Model
{
    use HasFactory;

    protected $table = "account_credit_history";

    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'time',
        'payment_option',
        'reference',
    ];
}
