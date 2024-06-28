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
        Schema::create('people', function (Blueprint $table) {
                $table->id();
                $table->string('gedcom_id')->unique()->nullable();
                $table->string('name');
                $table->char('gender', 1)->nullable();
                $table->date('birth_date')->nullable();
                $table->enum('birth_date_qualifier', ['ABT', 'BEF', 'AFT', 'EXACT'])->nullable();
                $table->date('death_date')->nullable();
                $table->enum('death_date_qualifier', ['ABT', 'BEF', 'AFT', 'EXACT'])->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
