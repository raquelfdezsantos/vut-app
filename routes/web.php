<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentReceiptMail;
use App\Models\Reservation;
use App\Models\Invoice;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('home');

// Propiedades
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/propiedad/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

/*
|--------------------------------------------------------------------------
| Rutas protegidas comunes (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Área ADMIN (/admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        // Stubs de vistas 
        Route::view('/property', 'admin.property.index')->name('property.index');
        Route::view('/photos',   'admin.photos.index')->name('photos.index');
        Route::view('/calendar', 'admin.calendar.index')->name('calendar.index');

        // Editar, actualizar y cancelar reserva
        Route::post('/reservations/{id}/cancel', [AdminController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/reservations/{id}/edit', [AdminController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{id}', [AdminController::class, 'update'])->name('reservations.update');

        // Reembolso de reserva
        Route::post('/reservations/{id}/refund', [AdminController::class, 'refund'])->name('reservations.refund');
    });

/*
|--------------------------------------------------------------------------
| Área CLIENTE (reservas)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Listado de reservas del cliente
    Route::get('/mis-reservas', [ReservationController::class, 'index'])->name('reservas.index');
    
    // Crear reserva (POST desde ficha)
    Route::post('/reservas', [ReservationController::class, 'store'])->name('reservas.store');
    
    // Listado de facturas del cliente
    Route::get('/mis-facturas', [InvoiceController::class, 'index'])->name('invoices.index');
    
    // Editar, actualizar y cancelar reserva
    Route::get('/reservas/{reservation}/editar', [ReservationController::class, 'edit'])->name('reservas.edit');
    Route::put('/reservas/{reservation}', [ReservationController::class, 'update'])->name('reservas.update');
    Route::post('/reservas/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservas.cancel');
    
    // Pagar diferencia de una reserva ya existente
    Route::post('/reservations/{id}/pay-difference', [PaymentController::class, 'payDifference'])
        ->name('reservations.pay_difference');

    // Cancelación por parte del cliente
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancelSelf'])
        ->middleware('role:customer')
        ->name('reservas.cancel.self');
});

/*
|--------------------------------------------------------------------------
| Pagos y facturas (comunes con auth)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::post('/reservations/{id}/pay', [PaymentController::class, 'pay'])->name('reservations.pay');
    Route::get('/invoices/{number}', [InvoiceController::class, 'show'])->name('invoices.show');
});

// Ruta de prueba envío email con Mailtrap
Route::get('/dev/test-payment-mail', function () {
    $reservation = Reservation::with(['user', 'property'])->latest()->firstOrFail();
    $invoice = Invoice::where('reservation_id', $reservation->id)->latest()->firstOrFail();
    \Mail::to('cliente@vut.test')->send(new PaymentReceiptMail($reservation, $invoice));
    return 'OK sent';
});


/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
