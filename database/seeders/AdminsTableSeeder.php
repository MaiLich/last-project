<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
        public function run()
    {
        


        
        
        
        $adminRecords = [
            [
                'id'        => 1,
                'name'      => 'Ahmed Yahya',
                'type'      => 'superadmin',
                'vendor_id' => 0, 
                'mobile'    => '9800000000',
                'email'     => 'admin@admin.com',
                'password'  => '$2a$12$xvkjSScUPRexfcJTAy9ATutIeGUuRgJrjDIdL/.xlrddEvRZINpeC', 
                'image'     => '',
                'status'    => 1,
            ],

            
            [
                'id'        => 2,
                'name'      => 'John Singh - Vendor',
                'type'      => 'vendor',
                'vendor_id' => 1, 
                'mobile'    => '9700000000',
                'email'     => 'john@admin.com',
                'password'  => '$2a$12$xvkjSScUPRexfcJTAy9ATutIeGUuRgJrjDIdL/.xlrddEvRZINpeC', 
                'image'     => '',
                'status'    => 1, 
            ],
        ];
        
        \App\Models\Admin::insert($adminRecords);
    }
}
