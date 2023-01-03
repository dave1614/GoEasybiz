<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'id' => 10,
            'name' => 'Admin One',
            'is_admin' => 1,
            'phone' => '08174622455',
            'email' => 'meetglobalresources@gmail.com',
            'country_id' => 151,
            'password' => Hash::make('Dave1614..'),
            'remember_token' => Str::random(10),
            'created' => 1,
        ]);

        User::create([
            'sponsor_user_id' => 10,
            'name' => "Nwogo David",
            'email' => "ikechukwunwogo@gmail.com",
            'country_id' => 151,
            'phone' => "07051942325",
            'password' => Hash::make("Dave1614.."),
            'remember_token' => Str::random(10),
        ]);

        // User::factory()->create();
    }
}
