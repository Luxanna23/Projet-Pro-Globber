<?php

use App\Http\Controllers\OwnerReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Models\Reservation;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/reservations', [ReservationController::class, 'index'])->name('profile.reservations');

    Route::resource('annonces', \App\Http\Controllers\AnnonceController::class);
});

Route::post('/annonces/{annonce}/reserver', [ReservationController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('annonces.reserve');

Route::get('/reservations/{reservation}/confirmation', function (Reservation $reservation) {
    $reservation->load('annonce', 'user');

    return Inertia::render('Reservations/Confirmation', [
        'reservation' => $reservation,
    ]);
})->middleware(['auth'])->name('reservations.confirmation');

Route::middleware(['auth'])->group(function () {
    Route::get('/proprietaire/reservations', [OwnerReservationController::class, 'index'])
        ->name('owner.reservations');

    Route::patch('/proprietaire/reservations/{reservation}/status', [OwnerReservationController::class, 'updateStatus'])
        ->name('owner.reservations.updateStatus');
});

require __DIR__.'/auth.php';
