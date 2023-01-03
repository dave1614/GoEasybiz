<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMonnifyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "account_reference",
        "acoount_name",
        "currency_code",
        "customer_email",
        "customer_name",
        "reservation_reference",
        "reserved_account_type",
        "wema_account_name",
        "wema_account_number",
        "sterling_account_name",
        "sterling_account_number",
        "fidelity_account_name",
        "fidelity_account_number",
        "moniepoint_account_name",
        "moniepoint_account_number",
    ];

    public function getUser(){
        return $this->belongsTo(User::class);
    }
}
