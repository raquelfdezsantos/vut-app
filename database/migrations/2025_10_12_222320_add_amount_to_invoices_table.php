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
        // SQLite doesn't support referencing another table alias in the SET clause the
        // same way MySQL/Postgres do with multi-table updates, so use a portable
        // subselect which works across drivers:
        DB::statement(<<<'SQL'
            UPDATE invoices
            SET amount = (
                SELECT total_price FROM reservations WHERE reservations.id = invoices.reservation_id
            )
            WHERE EXISTS (
                SELECT 1 FROM reservations WHERE reservations.id = invoices.reservation_id
            )
        SQL
        );
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('amount');
            // Si se hizo nullable en pdf_path arriba, aquí se podría revertir
        });
    }
};
