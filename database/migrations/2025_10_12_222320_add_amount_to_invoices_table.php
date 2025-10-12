<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->after('number')->default(0);
            // (opcional pero recomendable) permitir null en pdf_path
            $table->string('pdf_path', 255)->nullable()->change();
        });

        // Backfill: poner el total de la reserva en amount
        DB::table('invoices as i')
            ->join('reservations as r', 'r.id', '=', 'i.reservation_id')
            ->update(['i.amount' => DB::raw('r.total_price')]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('amount');
            // Si se hizo nullable en pdf_path arriba, aquí se podría revertir
        });
    }
};
