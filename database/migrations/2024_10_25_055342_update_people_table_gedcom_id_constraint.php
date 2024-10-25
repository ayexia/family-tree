<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::table('people', function (Blueprint $table) {
        $table->dropUnique(['gedcom_id']);
        
        $table->unique(['gedcom_id', 'family_tree_id']);
    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            
            $table->dropUnique(['gedcom_id', 'family_tree_id']);
            $table->unique(['gedcom_id']);
        });
    }
};
