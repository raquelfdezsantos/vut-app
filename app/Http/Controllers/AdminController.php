<?php
/**
 * -----------------------------------------------------------------------------
 *  Proyecto: VUT App (2º DAW)
 *  Archivo: AdminController.php
 *  Autoría: Raquel Fernández Santos
 *  Descripción:
 *    Controlador del área de administración. Permite:
 *      - Ver el listado de reservas con filtros básicos.
 *      - Cancelar reservas pendientes y reponer noches en el calendario.
 *  Notas:
 *    - Requiere middleware de autenticación y rol 'admin'.
 *    - La reposición de noches afecta a RateCalendar indicando disponibilidad=true.
 * -----------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RateCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard del administrador con el listado de reservas.
     *
     * Filtros opcionales por estado (?status=pending|paid|cancelled) y por rango
     * de fechas (?from=YYYY-MM-DD&to=YYYY-MM-DD).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'property', 'invoice'])->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($from = $request->date('from')) {
            $query->whereDate('check_in', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $query->whereDate('check_out', '<=', $to);
        }

        $reservations = $query->paginate(10)->withQueryString();

        return view('admin.dashboard', compact('reservations'));
    }

    /**
     * Cancela una reserva "pending" y repone las noches al calendario.
     *
     * Reglas:
     *  - Solo reservas con estado 'pending' pueden cancelarse aquí.
     *  - Reposición: marca como disponibles (is_available=true) las fechas del
     *    rango [check_in, check_out) para la propiedad asociada.
     *
     * @param  int  $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(int $reservationId)
    {
        $reservation = Reservation::query()
            ->where('id', $reservationId)
            ->with('property')
            ->firstOrFail();

        // Solo cancelamos si está pendiente
        if ($reservation->status !== 'pending') {
            return back()->with('error', 'Solo es posible cancelar reservas pendientes.');
        }

        DB::transaction(function () use ($reservation) {
            // 1) Actualiza estado de la reserva
            $reservation->update(['status' => 'cancelled']);

            // 2) Restaura disponibilidad en RateCalendar
            $start = $reservation->check_in->copy();
            $end   = $reservation->check_out->copy();

            // Reponemos cada día del rango [check_in, check_out)
            for ($date = $start->copy(); $date->lt($end); $date->addDay()) {
                RateCalendar::where('property_id', $reservation->property_id)
                    ->whereDate('date', $date->toDateString())
                    ->update(['is_available' => true]);
            }
        });

        return back()->with('success', 'Reserva cancelada y noches repuestas correctamente.');
    }
}
