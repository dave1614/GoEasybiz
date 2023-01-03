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
        Schema::create('mlm_db', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            // $table->integer('package')->nullable();
            $table->integer('sponsor')->nullable();
            $table->integer('under')->nullable();
            $table->integer('stage')->nullable();
            $table->string('positioning')->nullable();
            $table->string('date_created')->nullable();
            $table->string('time_created')->nullable();
            $table->string('reference')->nullable();
            $table->string('last_subscription_date')->nullable();
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
        Schema::dropIfExists('mlm_db');
    }
};
