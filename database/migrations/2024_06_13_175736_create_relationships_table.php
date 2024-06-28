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
        Schema::create('relationships', function (Blueprint $table) {
            $table->id();
            $table->string('gedcom_id')->unique();
            $table->foreignId('person_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->foreignId('relative_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->string('type');
            $table->date('marriage_date')->nullable();
            $table->string('marriage_date_qualifier')->nullable();
            $table->date('divorce_date')->nullable();
            $table->string('divorce_date_qualifier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationships');
    }
};
