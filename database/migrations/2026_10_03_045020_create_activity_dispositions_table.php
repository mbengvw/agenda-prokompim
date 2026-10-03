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
        Schema::create('activity_dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_leader_id')->constrained('leaders')->cascadeOnDelete();
            $table->foreignId('to_leader_id')->nullable()->constrained('leaders')->cascadeOnDelete();
            $table->string('status'); // hadir, skip, disposisi
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_dispositions');
    }
};
