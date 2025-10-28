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
        Schema::table('rate_calendars', function (Blueprint $table) {
            // Campo para distinguir el tipo de bloqueo
            // null = fecha disponible
            // 'reservation' = bloqueada por reserva de cliente
            // 'admin' = bloqueada manualmente por administrador
            $table->enum('blocked_by', ['reservation', 'admin'])->nullable()->after('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rate_calendars', function (Blueprint $table) {
            $table->dropColumn('blocked_by');
        });
    }
};
