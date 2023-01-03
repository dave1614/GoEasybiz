<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MlmDb extends Model
{
    use HasFactory;

    protected $table = 'mlm_db';

    protected $fillable = [
        'user_id',
        'sponsor',
        'under',
        'stage',
        'positioning',
        'date_created',
        'time_created',
        'reference',
    ];
}
