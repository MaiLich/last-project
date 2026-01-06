<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FiltersTableSeeder extends Seeder
{
        public function run()
    {
        



        
        
        $filterRecords = [
            [
                'id'            => 1,
                'cat_ids'       => '1,2,3,6,7,8', 
                'filter_name'   => 'Fabric', 
                'filter_column' => 'fabric',
                'status'        => 1,
            ],
            [
                'id'            => 2,
                'cat_ids'       => '4,5', 
                'filter_name'   => 'RAM', 
                'filter_column' => 'ram', 
                'status'        => 1,
            ],
        ];
        
        \App\Models\ProductsFilter::insert($filterRecords);
    }
}
