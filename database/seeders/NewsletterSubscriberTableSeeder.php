<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsletterSubscriberTableSeeder extends Seeder
{
        public function run(): void
    {
        
        
        $subscriberRecords = [
            [
                'id'     => 1,
                'email'  => 'yasser100@yopmail.com',
                'status' => 1
            ],
            [
                'id'     => 2,
                'email'  => 'fouaad@gmail.com',
                'status' => 1
            ]
        ];

        
        \App\Models\NewsletterSubscriber::insert($subscriberRecords);
    }
}
