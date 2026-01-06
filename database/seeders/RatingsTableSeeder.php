<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingsTableSeeder extends Seeder
{
        public function run(): void
    {
        
        
        $ratingRecords = [
            [
                'id'         => 1,
                'user_id'    => 2,
                'product_id' => 1,
                'review'     => 'It\'s a great mobile phone!',
                'rating'     => 4,
                'status'     => 1
            ],
            [
                'id'         => 2,
                'user_id'    => 2,
                'product_id' => 2,
                'review'     => 'Awesome product!',
                'rating'     => 5,
                'status'     => 1
            ]
        ];

        
        \App\Models\Rating::insert($ratingRecords);
    }
}
