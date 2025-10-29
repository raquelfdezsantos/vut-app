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
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ContactController;

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

        // Gestión de propiedad
        Route::get('/property', [AdminController::class, 'propertyEdit'])->name('property.index');
        Route::put('/property/{property}', [AdminController::class, 'propertyUpdate'])->name('property.update');
        Route::delete('/property/{property}', [AdminController::class, 'destroyProperty'])->name('property.destroy');

        // Gestión de fotos
        Route::get('/photos', [AdminController::class, 'photosIndex'])->name('photos.index');
        Route::post('/photos', [AdminController::class, 'photosStore'])->name('photos.store');
        Route::delete('/photos/{photo}', [AdminController::class, 'photosDestroy'])->name('photos.destroy');
        Route::post('/photos/reorder', [AdminController::class, 'photosReorder'])->name('photos.reorder');
        Route::post('/photos/{photo}/set-cover', [AdminController::class, 'photosSetCover'])->name('photos.set-cover');

        // Calendario
        Route::view('/calendar', 'admin.calendar.index')->name('calendar.index');
        Route::post('/calendar/block',   [AdminController::class, 'blockDates'])->name('calendar.block');
        Route::post('/calendar/unblock', [AdminController::class, 'unblockDates'])->name('calendar.unblock');

        // Reservas
        Route::post('/reservations/{id}/cancel', [AdminController::class, 'cancel'])->name('reservations.cancel');
        Route::get('/reservations/{id}/edit', [AdminController::class, 'edit'])->name('reservations.edit');
        Route::put('/reservations/{id}', [AdminController::class, 'update'])->name('reservations.update');
        Route::post('/reservations/{id}/refund', [AdminController::class, 'refund'])->name('reservations.refund');

        // Listado de facturas
        Route::get('/invoices', [InvoiceController::class, 'adminIndex'])
            ->name('invoices.index');
        Route::get('/invoices/{number}', [InvoiceController::class, 'show'])
            ->name('invoices.show');
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
| Stripe (test)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Crear sesión de Stripe Checkout (POST)
    Route::post('/checkout/{reservation}', [StripeController::class, 'checkout'])
        ->name('stripe.checkout');

    // URLs de retorno desde Stripe (GET)
    Route::get('/checkout/success', [StripeController::class, 'success'])
        ->name('stripe.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])
        ->name('stripe.cancel');
});


/*
|--------------------------------------------------------------------------
| Formulario de contacto
|--------------------------------------------------------------------------
*/

Route::get('/contacto', [ContactController::class, 'create'])->name('contact.create');
// Alias /contact que redirige al mismo formulario (opcional)
Route::get('/contact', fn() => redirect()->route('contact.create'));

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1') // máx. 5 envíos por minuto
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
