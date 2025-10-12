<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');

// Propiedades
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/propiedad/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

/*
|--------------------------------------------------------------------------
| Rutas protegidas comunes (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
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

        // Acciones sobre reservas
        Route::post('/reservations/{id}/cancel', [AdminController::class, 'cancel'])->name('reservations.cancel');
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

    // Pago simulado (coherente con PaymentController::pay)
    Route::post('/pagos/{reservation}/simular', [PaymentController::class, 'pay'])->name('payments.pay');
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

/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
