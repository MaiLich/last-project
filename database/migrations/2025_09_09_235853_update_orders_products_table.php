<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up()
    {
        
        Schema::table('orders_products', function($table) {
            $table->string('courier_name')->after('item_status')->nullable();
            $table->string('tracking_number')->after('courier_name')->nullable();
            $table->float('commission')->nullable(); 
        });
    }

        public function down()
    {
        
        Schema::table('orders_products', function($table) {
            $table->dropColumn('courier_name');
            $table->dropColumn('tracking_number');
        });
    }
};
