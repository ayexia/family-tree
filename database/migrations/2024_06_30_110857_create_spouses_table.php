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
        Schema::create('spouses', function (Blueprint $table) {
            $table->id();
            $table->string('gedcom_id');
            $table->foreignId('first_spouse_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->foreignId('second_spouse_id')->nullable()->constrained('people')->onDelete('cascade');
            $table->date('marriage_date')->nullable();
            $table->enum('marriage_date_qualifier', ['ABT', 'BEF', 'AFT', 'EXACT'])->nullable();
            $table->date('divorce_date')->nullable();
            $table->enum('divorce_date_qualifier', ['ABT', 'BEF', 'AFT', 'EXACT'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spouses');
    }
};
