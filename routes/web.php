<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AdminController;

/*
* Rutas públicas
*/
Route::get('/', fn () => view('welcome'))->name('home');

/** Propiedades (listado + ficha) */
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/propiedad/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');


/*
* Rutas protegidas (Breeze)
*/
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
* Área admin
*/
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', fn () => view('admin.dashboard'))->name('admin.dashboard');
});


/*
* Cliente (reservas)
*/
// Recomendado: exigir rol "customer".
Route::middleware(['auth','role:customer'])->group(function () {
    // Listado de reservas del cliente
    Route::get('/mis-reservas', [ReservationController::class, 'index'])->name('reservas.index');

    // Crear reserva (POST desde la ficha de propiedad)
    Route::post('/reservas', [ReservationController::class, 'store'])->name('reservas.store');
});


/*
 * -----------------------------------------------------------------------------
 * Rutas de administración
 *
 * Prefijo /admin, nombre admin.*, controlador AdminController.
 * Requiere middleware de autenticación y rol 'admin'.
 */
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/reservations/{id}/cancel', [AdminController::class, 'cancel'])
            ->name('reservations.cancel');
    });


    
/*
* Auth (Breeze)
*/
require __DIR__.'/auth.php';
