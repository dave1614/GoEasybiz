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
        Schema::create('mlm_income_history', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->string("income_type")->nullable();
            $table->integer("package")->nullable();
            $table->integer("creditors_id")->nullable();
            $table->decimal("amount", 20,2)->default(0.00);
            $table->integer("vat")->nullable();
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
        Schema::dropIfExists('mlm_income_history');
    }
};


