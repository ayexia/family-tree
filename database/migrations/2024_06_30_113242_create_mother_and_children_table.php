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
        Schema::create('mother_and_children', function (Blueprint $table) {
            $table->id();
            $table->string('gedcom_id')->unique()->nullable();
            $table->foreignId('mother_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->foreignId('child_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->integer('child_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mother_and_children');
    }
};
