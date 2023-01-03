<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonnifyPaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_reference',
        'transaction_reference',
        'payment_reference',
        'amount_paid',
        'total_payable',
        'settlement_amount',
        'paid_on',
        'payment_status',
        'payment_description',
        'transaction_hash',
        'currency',
        'payment_method',
        'payment_accountName',
        'payment_accountNumber',
        'payment_bankCode',
        'payment_amountPaid',
    ];
}
