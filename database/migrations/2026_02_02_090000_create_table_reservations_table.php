<?php

use App\Models\TableReservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('table_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->foreignId('shop_user_id')->constrained('shop_users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedTinyInteger('table_number');
            $table->unsignedTinyInteger('party_size');
            $table->dateTime('reserved_for');
            $table->dateTime('reserved_until');
            $table->unsignedTinyInteger('status')->default(TableReservation::STATUS_PENDING);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['table_number', 'reserved_for']);
            $table->index(['shop_user_id', 'reserved_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_reservations');
    }
};
