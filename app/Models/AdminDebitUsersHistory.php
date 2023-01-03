<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDebitUsersHistory extends Model
{
    use HasFactory;

    protected $table = "admin_debit_users_history";
    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'time',
    ];
}
