<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboRechargeVtu extends Model
{
    use HasFactory;

    protected $table = "combo_recharge_vtu";

    protected $fillable = [
        'user_id',
        'number',
        'amount',
        'date',
        'time',
        'credited',
        'credited_date',
        'credited_time',
    ];
}
