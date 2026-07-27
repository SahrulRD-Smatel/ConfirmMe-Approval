<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Add unique composite index on attendances table for upsert support.
     * Unique key: user_id + date + type
     *
     * This ensures:
     * 1. Each employee can only have one check_in and one check_out per day
     * 2. Laravel's upsert() can work correctly with this unique constraint
     * 3. Import operations can efficiently detect existing vs new records
     *
     * Before adding the constraint, we clean up any duplicate records
     * (keeping the most recent one) to prevent migration failure.
     */
    public function up(): void
    {
        // Clean up potential duplicate records before adding unique index.
        // Keep the record with the latest created_at for each (user_id, date, type) group.
        $duplicates = DB::table('attendances')
            ->select('user_id', 'date', 'type')
            ->groupBy('user_id', 'date', 'type')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $keepId = DB::table('attendances')
                ->where('user_id', $dup->user_id)
                ->where('date', $dup->date)
                ->where('type', $dup->type)
                ->orderByDesc('created_at')
                ->value('id');

            DB::table('attendances')
                ->where('user_id', $dup->user_id)
                ->where('date', $dup->date)
                ->where('type', $dup->type)
                ->where('id', '!=', $keepId)
                ->delete();
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['user_id', 'date', 'type'], 'attendances_user_date_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_user_date_type_unique');
        });
    }
};
