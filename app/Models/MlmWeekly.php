<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlmWeekly extends Model
{
    use HasFactory;
    protected $table = "mlm_weekly";

    protected $fillable = [
        'user_id',
        'week_start',
        'total_sponsor',
        'total_placement',
        'total_upteam',
    ];
}
