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
        Schema::create('mlm_weekly', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->timestamp("week_start")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal("total_sponsor", 20, 2)->default(0.00);
            $table->decimal("total_placement", 20, 2)->default(0.00);
            $table->decimal("total_upteam", 20, 2)->default(0.00);
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
        Schema::dropIfExists('mlm_weekly');
    }
};
