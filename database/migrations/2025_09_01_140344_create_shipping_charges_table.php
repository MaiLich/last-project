<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::create('shipping_charges', function (Blueprint $table) {
            
            $table->id();

            $table->string('country');
            $table->float('rate'); 
            $table->tinyInteger('status');

            $table->timestamps();
        });
    }

        public function down(): void
    {
        Schema::dropIfExists('shipping_charges');
    }
};
