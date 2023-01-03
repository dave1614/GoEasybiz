<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeptSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('depts')->delete();
        
        \DB::table('depts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'pathology laboratory services',
                'slug' => 'pathology-laboratory-services',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'clinic Services',
                'slug' => 'clinic-services',
            ),
            2 => 
            array (
                'id' => 7,
                'name' => 'mortuary',
                'slug' => 'mortuary',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'wards',
                'slug' => 'wards',
            ),
            4 => 
            array (
                'id' => 6,
                'name' => 'pharmacy',
                'slug' => 'pharmacy',
            ),
            5 => 
            array (
                'id' => 8,
                'name' => 'records',
                'slug' => 'records',
            ),
            6 => 
            array (
                'id' => 9,
                'name' => 'finance',
                'slug' => 'finance',
            ),
        ));
        
        
    }
}