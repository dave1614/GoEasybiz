<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonnifyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log',
    ];
}
