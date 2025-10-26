<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['reservation.property'])
            ->whereHas('reservation', fn($q) => $q->where('user_id', Auth::id()))
            ->latest('issued_at')
            ->paginate(10);

        return view('customer.invoices.index', compact('invoices'));
    }

    /**
     * Muestra los detalles de una factura específica.
     *
     * @param string $number Número de la factura a mostrar.
     * @return \Illuminate\View\View Vista con los detalles de la factura.
     */
    public function show(string $number)
    {
        $invoice = Invoice::with(['reservation.user', 'reservation.property'])
            ->where('number', $number)->firstOrFail();

        $this->authorize('view', $invoice->reservation);

        if (request()->boolean('download')) {
            $pdf = PDF::loadView('invoices.pdf', ['invoice' => $invoice]);
            return $pdf->download($invoice->number . '.pdf');
        }

        return view('invoices.show', compact('invoice'));
    }


    /**
     * Summary of adminIndex
     * @return \Illuminate\Contracts\View\View
     */
    public function adminIndex()
    {
        $invoices = Invoice::with(['reservation.user', 'reservation.property'])
            ->latest('issued_at')
            ->paginate(20);

        return view('admin.invoices.index', compact('invoices'));
    }
}
