<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorsBankDetailsTableSeeder extends Seeder
{
        public function run()
    {

        
        
        $vendorsBankDetailsRecords = [
            [
                'id'                  => 1,
                'vendor_id'           => 1,
                'account_holder_name' => 'John Cena',
                'bank_name'           => 'ICICI',
                'account_number'      => '021546545454541545454',
                'bank_ifsc_code'      => '36546655',
            ],
        ];
        
        \App\Models\VendorsBankDetail::insert($vendorsBankDetailsRecords);
    }
}
