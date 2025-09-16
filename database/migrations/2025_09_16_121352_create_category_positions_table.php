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
        Schema::create('category_positions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('category_id');
            $table->integer('position');
            $table->timestamps();

            $table->unique(['date','category_id'], 'uq_category_date_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_positions');
    }
};
