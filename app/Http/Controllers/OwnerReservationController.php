<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class OwnerReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Récupère toutes les réservations liées aux annonces du propriétaire
        $allReservations = Reservation::with(['annonce', 'user'])
            ->whereHas('annonce', fn($q) => $q->where('user_id', $user->id))
            ->get();

        // On regroupe les réservations terminées à part (peu importe le statut)
        $finished = $allReservations->filter(fn($r) =>
            Carbon::parse($r->end_date)->isPast()
        );

        // On regroupe le reste par statut
        $grouped = $allReservations->filter(fn($r) =>
            Carbon::parse($r->end_date)->isFuture()
        )->groupBy('status');

        // Ajoute manuellement les terminées
        $grouped['finished'] = $finished;

        return Inertia::render('Owner/Reservations', [
            'reservations' => $grouped,
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
