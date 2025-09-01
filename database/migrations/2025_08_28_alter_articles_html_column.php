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
        // First make the column nullable to avoid any constraints
        Schema::table('articles', function (Blueprint $table) {
            $table->text('html')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Revert back to string type (varchar) if needed
            $table->string('html')->change();
        });
    }
};
