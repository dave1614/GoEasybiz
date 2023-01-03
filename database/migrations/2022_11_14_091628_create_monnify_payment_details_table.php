<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monnify_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->decimal("amount_credited", 20, 2)->default(0.00);
            $table->string("account_reference")->nullable();
            $table->string("transaction_reference")->nullable();
            $table->string("payment_reference")->nullable();
            $table->decimal("amount_paid", 20,2)->default(0.00);
            $table->decimal("total_payable", 20,2)->default(0.00);
            $table->decimal("settlement_amount", 20,2)->default(0.00);
            $table->string("paid_on")->nullable();
            $table->string("payment_status")->nullable();
            $table->text("payment_description")->nullable();
            $table->text("transaction_hash")->nullable();
            $table->string("currency")->nullable();
            $table->string("payment_method")->nullable();
            $table->string("payment_accountName")->nullable();
            $table->string("payment_accountNumber")->nullable();
            $table->string("payment_bankCode")->nullable();
            $table->decimal("payment_amountPaid", 20,2)->default(0.00);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('monnify_payment_details');
    }
};
