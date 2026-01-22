<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('code', 12)->nullable()->after('id');
        });

        DB::table('reservations')->orderBy('id')->chunkById(100, function ($reservations) {
            foreach ($reservations as $reservation) {
                if (! $reservation->code) {
                    do {
                        $code = strtoupper(Str::random(8));
                    } while (DB::table('reservations')->where('code', $code)->exists());

                    DB::table('reservations')
                        ->where('id', $reservation->id)
                        ->update(['code' => $code]);
                }
            }
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('code', 12)->nullable(false)->change();
            $table->unique('code', 'reservations_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
