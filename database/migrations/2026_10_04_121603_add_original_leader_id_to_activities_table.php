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
            $table->foreignId('original_leader_id')->nullable()->constrained('leaders')->nullOnDelete()->after('leader_id');
        });

        // Populate existing data
        \Illuminate\Support\Facades\DB::statement("
            UPDATE activities SET original_leader_id = leader_id
        ");

        \Illuminate\Support\Facades\DB::statement("
            UPDATE activities 
            SET original_leader_id = (
                SELECT from_leader_id 
                FROM activity_dispositions 
                WHERE activity_dispositions.activity_id = activities.id 
                ORDER BY id ASC 
                LIMIT 1
            )
            WHERE id IN (SELECT activity_id FROM activity_dispositions)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['original_leader_id']);
            $table->dropColumn('original_leader_id');
        });
    }
};
