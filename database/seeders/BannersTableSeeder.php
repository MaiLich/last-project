<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannersTableSeeder extends Seeder
{
        public function run()
    {
        

        
        
        
        $bannerRecords = [
            [
                'id'     => 1,
                'image'  => 'banner-1.jpg',
                'type'   => 'Slider',
                'link'   => 'spring-collection', 
                'title'  => 'Spring Collection',
                'alt'    => 'Spring Collection',
                'status' => 1
            ],
            [
                'id'     => 2,
                'image'  => 'banner-2.jpg',
                'type'   => 'Slider',
                'link'   => 'tops', 
                'title'  => 'Tops',
                'alt'    => 'Tops',
                'status' => 1
            ],
        ];
        
        \App\Models\Banner::insert($bannerRecords);
    }
}
