<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use MustVerifyNewEmail, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sponsor_user_id',
        'name',
        'user_name',
        'slug',
        'email',
        'country_id',
        'region_id',
        'is_admin',
        'phone',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function country(){
        return $this->hasOne(Country::class);
    }

    protected function phoneCode(): Attribute
    {
        return Attribute::make(
            get: fn ($value,$attributes) => Country::find($attributes['country_id'])->phone_code,
        );
    }

    public function adminDebitHistory()
    {
        return $this->hasMany(AdminDebitUsersHistory::class);
    }

    public function monnifyDetails(){
        return $this->hasMany(UserMonnifyDetail::class);
    }

    public function adminCreditHistory()
    {
        return $this->hasMany(AdminFundUsersHistory::class);
    }

    public function accountStatement() {
        return $this->hasMany(AccountStatement::class);
    }

    public function earningToWalletHist(){
        return $this->hasMany(EarningToWalletHistory::class);
    }

    public function accountCreditHistory()
    {
        return $this->hasMany(AccountCreditHistory::class);
    }

    public function withdrawalHistory()
    {
        return $this->hasMany(WithdrawalHistory::class);
    }
}
