<?php

namespace Database\Seeders;



use App\Models\ProductsFilter;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {














        $this->call(AdminsTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
        $this->call(VendorsBusinessDetailsTableSeeder::class);
        $this->call(VendorsBankDetailsTableSeeder::class);
        $this->call(SectionsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ProductsAttributesTableSeeder::class);
        $this->call(BannersTableSeeder::class);
        $this->call(FiltersTableSeeder::class);
        $this->call(FiltersValuesTableSeeder::class);
        $this->call(CouponsTableSeeder::class);
        $this->call(DeliveryAddressTableSeeder::class);
        $this->call(OrderStatusTableSeeder::class);
        $this->call(OrderItemStatusTableSeeder::class);
        $this->call(NewsletterSubscriberTableSeeder::class);
        $this->call(RatingsTableSeeder::class);
    }
}
