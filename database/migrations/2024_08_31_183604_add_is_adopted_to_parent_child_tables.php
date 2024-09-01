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
        Schema::table('mother_and_children', function (Blueprint $table) {
            $table->boolean('is_adopted')->default(false);
        });

        Schema::table('father_and_children', function (Blueprint $table) {
            $table->boolean('is_adopted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mother_and_children', function (Blueprint $table) {
            $table->dropColumn('is_adopted');
        });

        Schema::table('father_and_children', function (Blueprint $table) {
            $table->dropColumn('is_adopted');
        });
    }
};
