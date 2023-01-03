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
        Schema::create('vtu_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->string("type")->nullable();
            $table->string("sub_type")->nullable();
            $table->decimal("amount")->default(0.00);
            $table->string("number")->nullable();
            $table->string("order_id")->nullable();
            $table->boolean("reloadly")->default(false);
            $table->boolean("approved")->default(false);
            $table->boolean("refunded")->default(false);
            $table->string("date")->nullable();
            $table->string("time")->nullable();
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
        Schema::dropIfExists('vtu_transactions');
    }
};
