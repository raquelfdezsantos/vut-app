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
use App\Models\Property;
use App\Http\Controllers\QuoteController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Home adaptativa: si hay 1 propiedad, mostrar Home single; si hay >1, Home multi
    $count = Property::count();
    if ($count === 1) {
        $property = Property::with('photos')->first();
        return view('home-single', compact('property'));
    }

    // Si hay varias, permitimos destacar una por slug conocido si existe; si no, usamos multi
    $highlightSlug = 'apartamento-nordeste';
    $highlight = Property::with('photos')->where('slug', $highlightSlug)->first();
    if ($highlight && $count > 1) {
        // Podemos seguir mostrando multi igualmente; si en el futuro quieres un hero destacado, lo añadimos
        $properties = Property::with('photos')->orderBy('id')->get();
        return view('home-multi', compact('properties'));
    }

    $properties = Property::with('photos')->orderBy('id')->get();
    return view('home-multi', compact('properties'));
})->name('home');

// Propiedades
Route::get('/propiedades', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/propiedad/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

// Contacto (único formulario + mapa)
Route::get('/contacto', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contacto', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::get('/contact', fn() => redirect()->route('contact.create'));

// Entorno y Reservar (páginas públicas independientes - placeholders iniciales)
Route::view('/entorno', 'entorno.index')->name('entorno');
Route::view('/reservar', 'reservar.index')->name('reservar');
// Páginas legales
Route::get('/aviso-legal', fn() => view('legal.aviso-legal'))->name('legal.aviso');
Route::get('/politica-privacidad', fn() => view('legal.politica-privacidad'))->name('legal.privacidad');
Route::get('/cookies', fn() => view('legal.cookies'))->name('legal.cookies');

// (Eliminada ruta legacy /reservar -> public.reservar)


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
        // Dashboard principal (redirige al listado de propiedades)
        Route::get('/', function() {
            return redirect()->route('admin.properties.index');
        })->name('dashboard');

        // Gestión de PROPIEDADES (nuevo sistema)
        Route::get('/properties', [AdminController::class, 'propertiesIndex'])->name('properties.index');
        Route::get('/properties/create', [AdminController::class, 'propertiesCreate'])->name('properties.create');
        Route::post('/properties', [AdminController::class, 'propertiesStore'])->name('properties.store');
        Route::get('/properties/{property}', [AdminController::class, 'propertyDashboard'])->name('properties.dashboard');
        Route::patch('/properties/{property}/restore', [AdminController::class, 'propertiesRestore'])->name('properties.restore');

        // Gestión de propiedad individual (legacy - ahora necesita property_id)
        Route::get('/property/{property?}', [AdminController::class, 'propertyEdit'])->name('property.index');
        Route::put('/property/{property}', [AdminController::class, 'propertyUpdate'])->name('property.update');
        Route::delete('/property/{property}', [AdminController::class, 'destroyProperty'])->name('property.destroy');

        // Gestión de fotos
        Route::get('/photos/{property?}', [AdminController::class, 'photosIndex'])->name('photos.index');
        Route::post('/photos', [AdminController::class, 'photosStore'])->name('photos.store');
        Route::delete('/photos/{photo}', [AdminController::class, 'photosDestroy'])->name('photos.destroy');
        Route::post('/photos/reorder', [AdminController::class, 'photosReorder'])->name('photos.reorder');
        Route::post('/photos/{photo}/set-cover', [AdminController::class, 'photosSetCover'])->name('photos.set-cover');

        // Calendario
        Route::get('/calendar', [AdminController::class, 'calendarIndex'])->name('calendar.index');
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
    
    // Pagar diferencia tras modificar reserva
    Route::post('/checkout/{reservation}/difference', [StripeController::class, 'checkoutDifference'])
        ->name('stripe.checkout.difference');

    // URLs de retorno desde Stripe (GET)
    Route::get('/checkout/success', [StripeController::class, 'success'])
        ->name('stripe.success');
    Route::get('/checkout/cancel', [StripeController::class, 'cancel'])
        ->name('stripe.cancel');
});

/*
|
|--------------------------------------------------------------------------
| (Se eliminó duplicado de formulario de contacto que causaba error de firma)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/
Route::get('/api/quote', [QuoteController::class, 'show'])->name('quote.show');


/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
