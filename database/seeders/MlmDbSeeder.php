<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MlmDbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('mlm_db')->delete();

        \DB::table('mlm_db')->insert(array(
            0 =>
            array(
                'id' => 1,
                'user_id' => 10,
                'sponsor' => NULL,
                'under' => NULL,
                'stage' => 0,
                'positioning' => NULL,
                'date_created' => '29 Jul 2019',
                'time_created' => '06:50:14am',
                'created_at' => '2019-07-29 05:50:14',
                'updated_at' => '2019-07-29 05:50:14',
            ),
           
            
        ));
        
 
    }
}
