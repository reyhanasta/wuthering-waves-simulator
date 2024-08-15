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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->default('/');
            $table->unsignedBigInteger('attribute');
            $table->unsignedBigInteger('weapon');
            $table->unsignedBigInteger('rarity');
            $table->enum('specifications',['limited','standard']);
            $table->timestamps();


            $table->foreign('attribute')->references('id')->on('character_attributes');
            $table->foreign('weapon')->references('id')->on('weapon_types');
            $table->foreign('rarity')->references('id')->on('rarities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
