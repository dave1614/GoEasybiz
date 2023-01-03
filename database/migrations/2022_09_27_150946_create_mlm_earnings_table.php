<?php

use App\Models\MlmDb;
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
        Schema::create('mlm_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->foreignIdFor(MlmDb::class)->constrained()->onDelete('cascade')->nullable();
            $table->integer('type')->nullable();
            $table->decimal('amount',20,2);
            $table->string('date')->nullable();
            $table->string('time')->nullable();
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
        Schema::dropIfExists('mlm_earnings');
    }
};
