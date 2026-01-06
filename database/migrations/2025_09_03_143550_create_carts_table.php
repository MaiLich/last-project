<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up()
    {
        Schema::create('carts', function (Blueprint $table) { 
            $table->id();

            $table->string('session_id');
            $table->integer('user_id')->nullable(); 
            $table->integer('product_id');
            $table->string('size');
            $table->integer('quantity');

            $table->timestamps();
        });
    }

        public function down()
    {
        Schema::dropIfExists('carts');
    }
};
