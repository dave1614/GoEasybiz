<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EasyLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'amount_paid',
        'last_date_paid',
        'paid_off',
        
    ];
}
