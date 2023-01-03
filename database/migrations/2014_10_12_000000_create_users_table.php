<?php

use App\Models\Country;
use App\Models\Facility;
use App\Models\Region;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->default(false);
            $table->decimal("total_upteam_earnings", 20, 2)->default(0.00);
            $table->decimal("total_earnings", 20, 2)->default(0.00);
            $table->decimal("total_admin_earnings", 20, 2)->default(0.00);
            $table->decimal("total_withdrawan", 20, 2)->default(0.00);
            $table->string('name');
            // $table->string('user_name');
            // $table->string('slug');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_activity')->nullable();
            
            $table->text('providus_account_number')->nullable();
            $table->text('providus_account_name')->nullable();
            $table->text('bvn')->nullable();
            $table->text('sponsor_user_id')->nullable();
            $table->decimal('sponsor_income', 20, 2)->default(0.00);
            $table->decimal('registration_amt_paid', 20, 2)->default(0.00);
            $table->decimal('withdrawn', 20, 2)->default(0.00);
            $table->decimal('total_income', 20, 2)->default(0.00);
            $table->decimal('admin_mlm_withdrawn', 20, 2)->default(0.00);
            $table->string('phone_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('title')->nullable();
            
            $table->string('profile_picture')->nullable();
            $table->string('cover_photo')->nullable();
            $table->text('bio')->nullable();
            $table->foreignIdFor(Country::class)->constrained()->onDelete('cascade')->nullable();
            // $table->foreignIdFor(Region::class)->constrained()->onDelete('cascade')->nullable();
            $table->text('address')->nullable();
            $table->boolean('register')->default(false);
            $table->boolean('created')->default(false);
            $table->string('created_date')->nullable();
            $table->string('created_time')->nullable();
            
            
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('recepient_code')->nullable();
            
            $table->string('referred_by')->nullable();
            $table->string('mlm_withdrawn')->nullable();
            $table->decimal('admin_vat_total', 20, 2)->default(0.00);
            $table->decimal('total_basic_withdrawn', 20, 2)->default(0.00);
            $table->decimal('total_basic_income', 20, 2)->default(0.00);
            $table->decimal('total_business_income', 20, 2)->default(0.00);
            
            
            
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
