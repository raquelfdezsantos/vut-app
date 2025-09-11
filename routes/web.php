<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Rutas protegidas con middleware de autenticación y verificación (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas de administración
Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Rutas de cliente
Route::middleware(['auth','role:customer'])->group(function () {
    Route::get('/mis-reservas', function () {
        return view('customer.bookings');
    })->name('customer.bookings');
});



// Propiedad (pública): detalle + formulario reserva

Route::get('/propiedad/{slug}', [ReservationController::class, 'create'])->name('property.show');


// Cliente autenticado

Route::middleware(['auth','role:customer'])->group(function () {
    Route::post('/reservas', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/mis-reservas', [ReservationController::class, 'myBookings'])->name('customer.bookings');
});


// Autenticación (Breeze)
require __DIR__.'/auth.php';
