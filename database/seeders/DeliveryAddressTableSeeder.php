<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryAddressTableSeeder extends Seeder
{
        public function run()
    {
        
        
        $deliveryRecords = [
            [
                'id'      => 1,
                'user_id' => 1,
                'name'    => 'Ahmed Yahya',
                'address' => '37 Salah Salem',
                'city'    => 'Cairo',
                'state'   => 'Cairo',
                'country' => 'Egypt',
                'pincode' => 10001,
                'mobile'  => 1255642718,
                'status'  => 1
            ],
            [
                'id'      => 2,
                'user_id' => 1, 
                'name'    => 'Ahmed Yahya',
                'address' => '15 Fouaad St.',
                'city'    => 'Alexandria',
                'state'   => 'Alexandria',
                'country' => 'Egypt',
                'pincode' => 141001,
                'mobile'  => 1095632526,
                'status'  => 1
            ],
        ];

        
        \App\Models\DeliveryAddress::insert($deliveryRecords);
    }
}
