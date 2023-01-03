<?php

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
        Schema::create('notif', function (Blueprint $table) {
            $table->id();
            $table->string("sender")->nullable();
            $table->string("receiver")->nullable();
            $table->string("title")->nullable();
            $table->text("message")->nullable();
            $table->string("date_sent")->nullable();
            $table->string("time_sent")->nullable();
            $table->string("date_received")->nullable();
            $table->string("time_received")->nullable();
            $table->boolean("received")->default(false);
            $table->boolean("action_taken")->default(false);
            $table->string("post_id")->nullable();
            $table->string("type")->nullable();
            $table->text("btn_1")->nullable();
            $table->text("btn_2")->nullable();
            $table->text("btn_3")->nullable();
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
        Schema::dropIfExists('notif');
    }
};
