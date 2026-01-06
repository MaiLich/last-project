<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up()
    {
        Schema::create('products_filters_values', function (Blueprint $table) { 
            $table->id();

            $table->string('filter_id');
            $table->string('filter_value');
            $table->tinyInteger('status');

            $table->timestamps();
        });
    }

        public function down()
    {
        Schema::dropIfExists('products_filters_values');
    }
};
