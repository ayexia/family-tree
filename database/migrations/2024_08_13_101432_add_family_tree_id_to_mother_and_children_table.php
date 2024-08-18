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
            $table->foreignId('family_tree_id')
            ->nullable()
            ->constrained('family_trees')
            ->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mother_and_children', function (Blueprint $table) {
            $table->dropForeign(['family_tree_id']);
            $table->dropColumn('family_tree_id');
        });
    }
};
