<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonnifySubToken extends Model
{
    use HasFactory;

    protected $table = "monnify_sub_token";

    protected $fillable = [
        "bearer_token",
        "expires_in",
    ];
}
