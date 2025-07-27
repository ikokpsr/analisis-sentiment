<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aspect', function (Blueprint $table) {
            $table->id();
            $table->string('aspect');
            $table->integer('positif');
            $table->integer('negatif');
            $table->integer('netral');
            $table->integer('total');
            $table->float('persentasePositif', 5, 2);
            $table->float('persentaseNegatif', 5, 2);
            $table->float('persentaseNetral', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspect');
    }
};