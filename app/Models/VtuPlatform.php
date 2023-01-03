<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VtuPlatform extends Model
{
    use HasFactory;

    protected $table = "vtu_platform";

    protected $fillable = [
        "id",
        "name",
        "platform",
    ];
}
