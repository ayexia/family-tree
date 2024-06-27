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
            $table->unsignedBigInteger('person_id')->nullable();
            $table->unsignedBigInteger('relative_id')->nullable();
            $table->string('gedcom_id')->nullable();
            $table->enum('type', ['spouse', 'mother-child', 'father-child'])->nullable();
            $table->date('marriage_date')->nullable();
            $table->enum('marriage_date_qualifier', ['ABT', 'BEF', 'AFT', 'null'])->nullable();
            $table->date('divorce_date')->nullable();
            $table->enum('divorce_date_qualifier', ['ABT', 'BEF', 'AFT', 'null'])->nullable();
            $table->timestamps();
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');
            $table->foreign('relative_id')->references('id')->on('people')->onDelete('cascade');
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
