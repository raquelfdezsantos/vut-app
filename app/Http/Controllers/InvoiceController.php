<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

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
    public function show(string $invoice)
    {
        $inv = Invoice::with(['reservation.property','reservation.user'])
        ->where('number', $invoice)
        ->orWhere('id', $invoice)
        ->firstOrFail();

    return view('invoices.show', ['invoice' => $inv]);
    }
}
