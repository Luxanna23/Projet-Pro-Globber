<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OwnerReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Récupère les réservations liées aux annonces du propriétaire
        $reservations = Reservation::with(['annonce', 'user'])
            ->whereHas('annonce', fn($q) => $q->where('user_id', $user->id))
            ->get()
            ->groupBy('status');

        return Inertia::render('Owner/Reservations', [
            'reservations' => $reservations,
        ]);
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:accepted,refused',
        ]);

        // verifie que l'utilisateur est bien le propriétaire
        if ($reservation->annonce->user_id !== Auth::id()) {
            abort(403);
        }
        //si on passe à accepted et que ce n'était pas déjà le cas, on gagnes des points de fidélité pour la partie recompense
        if ($request->status === 'accepted' && $reservation->status !== 'accepted') {
            $reservation->user->increment('points', 25);

            // Marquer le pays comme visité
            $countryCode = $reservation->annonce->country_code; // le champ doit être en format ISO (FR, US...)

            if ($countryCode) {
                $alreadyVisited = $reservation->user->visitedCountries()
                    ->where('country_code', $countryCode)
                    ->exists();

                if (! $alreadyVisited) {
                    $reservation->user->visitedCountries()->create([
                        'country_code' => $countryCode,
                    ]);
                }
            }
        }
        $reservation->status = $request->status;
        $reservation->save();

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }
}
