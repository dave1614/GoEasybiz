<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EtisalatComboDataPlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('9mobile_combo_data_plans')->delete();
        
        \DB::table('9mobile_combo_data_plans')->insert(array (
            0 => 
            array (
                'id' => 11,
                'amount' => 5200,
                'data_amount' => '20GB',
            ),
            1 => 
            array (
                'id' => 9,
                'amount' => 2600,
                'data_amount' => '10GB',
            ),
            2 => 
            array (
                'id' => 7,
                'amount' => 520,
                'data_amount' => '2GB',
            ),
            3 => 
            array (
                'id' => 13,
                'amount' => 780,
                'data_amount' => '3GB',
            ),
            4 => 
            array (
                'id' => 12,
                'amount' => 1300,
                'data_amount' => '5GB',
            ),
            5 => 
            array (
                'id' => 14,
                'amount' => 1040,
                'data_amount' => '4GB',
            ),
        ));
        
        
    }
}