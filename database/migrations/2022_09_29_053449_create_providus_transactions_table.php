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
        Schema::create('providus_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('cascade');
            $table->text('amount_credited')->nullable();
            $table->text('sessionId')->nullable();
            $table->text('accountNumber')->nullable();
            $table->text('tranRemarks')->nullable();
            $table->text('transactionAmount')->nullable();
            $table->text('settledAmount')->nullable();
            $table->text('feeAmount')->nullable();
            $table->text('vatAmount')->nullable();
            $table->text('currency')->nullable();
            $table->text('initiationTranRef')->nullable();
            $table->text('settlementId')->nullable();
            $table->text('sourceAccountNumber')->nullable();
            $table->text('sourceAccountName')->nullable();
            $table->text('sourceBankName')->nullable();
            $table->text('channelId')->nullable();
            $table->text('tranDateTime')->nullable();
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
        Schema::dropIfExists('providus_transactions');
    }
};
