<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Reservation;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route pour récupérer les réservations par email pour mon site nosql
Route::get('/reservations/{email}', function (Request $request) {
    $email = $request->route('email');

    if (!$email) {
        return response()->json(['message' => 'Email manquant'], 400);
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
    }

    $reservations = Reservation::with('annonce')->where('user_id', $user->id)->get();

    return response()->json($reservations);
});