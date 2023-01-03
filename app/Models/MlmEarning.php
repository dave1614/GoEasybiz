<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlmEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mlm_db_id',
        'type',
        'amount',
        'date',
        'time',
    ];
}
