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
        Schema::create('credit_account_payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->string("depositors_name");
            $table->decimal("amount", 20, 2);
            $table->string("date_of_payment");
            $table->text("image");
            $table->string("date");
            $table->string("time");
            $table->boolean("credited")->default(false);
            $table->decimal("credited_amount", 20, 2)->default(0.00);
            $table->string("credited_date_time")->nullable();
            $table->boolean("dismissed")->default(false);
            $table->string("dismissed_date_time")->nullable();
            $table->timestamp("date_time");
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
        Schema::dropIfExists('credit_account_payment_proofs');
    }
};
