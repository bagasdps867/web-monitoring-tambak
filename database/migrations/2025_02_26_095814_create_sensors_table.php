<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->float('ph');
            $table->float('suhu');
            $table->float('tds'); 
            $table->float('kekeruhan');
            $table->float('kualitas')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};