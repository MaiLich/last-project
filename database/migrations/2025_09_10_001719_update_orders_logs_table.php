<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up()
    {
        
        Schema::table('orders_logs', function($table) {
            $table->integer('order_item_id')->after('order_id')->nullable(); 
        });
    }

        public function down()
    {
        
        Schema::table('orders_logs', function($table) {
            $table->dropColumn('order_item_id');
        });
    }
};
