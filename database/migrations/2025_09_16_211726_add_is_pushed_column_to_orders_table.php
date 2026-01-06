<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            
            
            $table->tinyInteger('is_pushed')->after('tracking_number')->default(0)->comment('Order pushed to Shiprocket or NOT'); 
        });
    }

        public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            
            $table->dropColumn('is_pushed');
        });
    }
};
