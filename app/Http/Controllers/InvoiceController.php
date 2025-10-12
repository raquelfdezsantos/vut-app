<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class InvoiceController extends Controller
{
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
