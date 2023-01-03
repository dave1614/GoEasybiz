<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminFundUsersHistory extends Model
{
    use HasFactory;

    protected $table = "admin_fund_users_history";
    protected $fillable = [
        'user_id',
        'amount',
        'date',
        'time',
    ];
}
