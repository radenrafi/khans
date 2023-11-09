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
        Schema::create('value_kriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained()->onDelete('cascade');
            $table->integer('value');
            $table->string('detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('value_kriterias');
    }
};
