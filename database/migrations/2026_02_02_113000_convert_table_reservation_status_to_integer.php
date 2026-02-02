<?php

use App\Models\TableReservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('table_reservations')) {
            return;
        }

        $mapping = [
            'pending' => TableReservation::STATUS_PENDING,
            'confirmed' => TableReservation::STATUS_CONFIRMED,
            'cancelled' => TableReservation::STATUS_CANCELLED,
        ];

        foreach ($mapping as $string => $code) {
            DB::table('table_reservations')
                ->where('status', $string)
                ->update(['status' => $code]);
        }

        DB::table('table_reservations')
            ->whereNotIn('status', array_values($mapping))
            ->update(['status' => TableReservation::STATUS_PENDING]);

        $driver = DB::getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement(sprintf(
                'ALTER TABLE table_reservations MODIFY status TINYINT UNSIGNED NOT NULL DEFAULT %d',
                TableReservation::STATUS_PENDING
            ));
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('table_reservations')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE table_reservations MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pending'");
        }

        $reverse = [
            TableReservation::STATUS_PENDING => 'pending',
            TableReservation::STATUS_CONFIRMED => 'confirmed',
            TableReservation::STATUS_CANCELLED => 'cancelled',
        ];

        foreach ($reverse as $code => $string) {
            DB::table('table_reservations')
                ->where('status', (string) $code)
                ->update(['status' => $string]);
        }
    }
};
