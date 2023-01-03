<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MlmChargesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('mlm_charges')->delete();
        
        \DB::table('mlm_charges')->insert(array (
            0 =>
            array(
                'id' => 1,
                'name' => 'Sign up amount',
                'amount' => '1100.00',
                'perc' => '0',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Weekly sub',
                'amount' => '1100.00',
                'perc' => '0',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Sponsor',
                'amount' => '0.00',
                'perc' => '75',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Placement',
                'amount' => '24.00',
                'perc' => '1',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Upteam',
                'amount' => '24.00',
                'perc' => '24',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Final earning',
                'amount' => '0.00',
                'perc' => '75',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Admin earning',
                'amount' => '0.00',
                'perc' => '1',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Airtime amount',
                'amount' => '500.00',
                'perc' => '0',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Amount to share',
                'amount' => '600.00',
                'perc' => '0',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'First Admin Earning',
                'amount' => '100.00',
                'perc' => '0',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Airtime Earning',
                'amount' => '0.00',
                'perc' => '0.1',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Data Earning',
                'amount' => '1.00',
                'perc' => '0',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Vtu Steps',
                'amount' => '24.00',
                'perc' => '0',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Funding Earning',
                'amount' => '1.00',
                'perc' => '0',
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Withdrawal Earning',
                'amount' => '1.00',
                'perc' => '0',
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Easy loan upline',
                'amount' => '24.00',
                'perc' => '10',
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'Resub Sponsor',
                'amount' => '0.00',
                'perc' => '9',
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Resub Placement',
                'amount' => '24.00',
                'perc' => '3.6',
            ),

        ));
        
        
    }
}