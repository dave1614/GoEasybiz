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
        Schema::create('loan_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->decimal("amount", 20, 2);
            $table->decimal("amount_paid", 20, 2);
            $table->bigInteger("charged_num");
            $table->decimal("service_charge", 20, 2);
            $table->text("summary");
            $table->string("date_time");
            $table->string("last_date_time_paid");
            $table->string("last_service_charge_date");
            $table->timestamp("datetime");
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
        Schema::dropIfExists('loan_advances');
    }
};


