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
        Schema::create('user_monnify_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->string("account_reference")->unique()->nullable();
            $table->string("acoount_name")->nullable();
            $table->string("currency_code")->nullable();
            $table->string("customer_email")->nullable();
            $table->string("customer_name")->nullable();
            $table->string("reservation_reference")->nullable();
            $table->string("reserved_account_type")->nullable();
            $table->string("wema_account_name")->nullable();
            $table->string("wema_account_number")->nullable();
            $table->string("sterling_account_name")->nullable();
            $table->string("sterling_account_number")->nullable();
            $table->string("fidelity_account_name")->nullable();
            $table->string("fidelity_account_number")->nullable();
            $table->string("moniepoint_account_name")->nullable();
            $table->string("moniepoint_account_number")->nullable();
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
        Schema::dropIfExists('user_monnify_details');
    }
};
