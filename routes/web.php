<?php

use App\Http\Controllers\OwnerReservationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScratchmapController;
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


Route::get('/', [HomeController::class, 'index'])->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route pour le profil 

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/reservations', [ReservationController::class, 'index'])->name('profile.reservations');

    Route::resource('annonces', AnnonceController::class);
    Route::get('/annonces', [AnnonceController::class, 'index'])->name('annonces.index');
});

// Route pour les réservations

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

// Route pour la messagerie

Route::get('/messages', function () {
    $user = auth()->user();

    $reservations = Reservation::with(['annonce.user', 'messages.sender'])
        ->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('annonce', fn($q) => $q->where('user_id', $user->id));
        })
        ->where('status', 'accepted')
        ->latest()
        ->get();

    // Ajoute le champ has_unread pour chaque réservation
    foreach ($reservations as $reservation) {
        $reservation->has_unread = $reservation->messages
            ->where('sender_id', '!=', $user->id)
            ->where('created_at', '>', $reservation->last_read_at)
            ->isNotEmpty();
    }

    return Inertia::render('Messages/Inbox', [
        'reservations' => $reservations,
        'userId' => $user->id,
    ]);
})->middleware(['auth', 'verified'])->name('messages.inbox');

Route::post('/reservations/{reservation}/messages', [MessageController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('messages.store');

Route::post('/messages/{reservation}/read', [MessageController::class, 'markAsRead'])
    ->middleware(['auth', 'verified'])
    ->name('messages.markAsRead');

// c'est pour le rafraichissement de la messagerie

Route::get('/messages/refresh', function () {
    $user = auth()->user();

    $reservations = Reservation::with(['annonce.user', 'messages.sender'])
        ->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('annonce', fn($q) => $q->where('user_id', $user->id));
        })
        ->where('status', 'accepted')
        ->latest()
        ->get();

    foreach ($reservations as $reservation) {
        $reservation->has_unread = $reservation->messages
            ->where('sender_id', '!=', $user->id)
            ->where('created_at', '>', $reservation->last_read_at)
            ->isNotEmpty();
    }

    return response()->json([
        'reservations' => $reservations
    ]);
})->name('messages.refresh');

//pour les notif des messages sur le menu

Route::get('/messages/unread-count', function () {
    $user = auth()->user();

    $reservations = Reservation::with('messages', 'annonce')
        ->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('annonce', fn($q) => $q->where('user_id', $user->id));
        })
        ->get();

    $hasUnread = $reservations->some(fn($r) =>
        $r->messages->where('sender_id', '!=', $user->id)
                    ->where('created_at', '>', $r->last_read_at)
                    ->isNotEmpty()
    );

    return response()->json(['has_unread' => $hasUnread]);
})->name('messages.unreadCount');

//Recompenses
Route::get('/profile/rewards', function () {
    $user = auth()->user();
    $reservations = Reservation::where('user_id', $user->id)->get();

    return Inertia::render('Profile/Rewards', [
        'reservations' => $reservations,
    ]);
})->middleware(['auth'])->name('profile.rewards');

require __DIR__.'/auth.php';

//scratch map
Route::get('/profile/scratchmap', [ScratchmapController::class, 'index'])
    ->middleware(['auth'])
    ->name('profile.scratchmap');