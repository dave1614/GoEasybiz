<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountStatement extends Model
{
    use HasFactory;

    protected $table = 'account_statement';
    protected $fillable = [
        'user_id',
        'amount',
        'amount_before',
        'amount_after',
        'summary',
        'date',
        'time',
    ];
}
