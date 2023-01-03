<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawal_request', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->decimal("amount", 20, 2)->default(0.00);
            $table->string("bank_name")->nullable();
            $table->string("real_bank_name")->nullable();
            $table->string("account_number")->nullable();
            $table->string("account_name")->nullable();
            $table->string("date")->nullable();
            $table->string("time")->nullable();
            $table->boolean("debited")->default(false);
            $table->string("debited_date_time")->nullable();
            $table->boolean("dismissed")->default(false);
            $table->string("dismissed_date_time")->nullable();
            $table->timestamp("date_time")->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('withdrawal_request');
    }
};
