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
        Schema::create('activity_companions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leader_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        
        // Remove the companions text column from activities table
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('companions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->text('companions')->nullable();
        });
        Schema::dropIfExists('activity_companions');
    }
};
