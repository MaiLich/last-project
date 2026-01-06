<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up()
    {
        Schema::create('orders_logs', function (Blueprint $table) {
            
            $table->id();

            $table->integer('order_id'); 
            $table->string('order_status');

            $table->timestamps();
        });
    }

        public function down()
    {
        Schema::dropIfExists('orders_logs');
    }
};
