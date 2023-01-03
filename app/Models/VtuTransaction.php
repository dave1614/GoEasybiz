<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VtuTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "type",
        "sub_type",
        "amount",
        "number",
        "order_id",
        "approved",
        "refunded",
        "date",
        "time",
    ];  
}
