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
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignId('leader_id')->nullable()->constrained('leaders')->nullOnDelete();
            $table->string('location_text')->nullable();
            $table->string('organizer_text')->nullable();
            $table->text('companions')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('adc')->nullable();
            $table->boolean('is_disposition')->default(false);
            $table->foreignId('disposition_to_id')->nullable()->constrained('leaders')->nullOnDelete();
            $table->text('revision_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropForeign(['disposition_to_id']);
            $table->dropColumn([
                'leader_id',
                'location_text',
                'organizer_text',
                'companions',
                'contact_person_name',
                'contact_person_phone',
                'adc',
                'is_disposition',
                'disposition_to_id',
                'revision_notes',
            ]);
        });
    }
};
