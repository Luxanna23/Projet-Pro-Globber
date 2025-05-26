<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Comment;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;


class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */ //cette ligne est pour indiquer que $user est de type User juste pcq vscode m'embete
        $user = auth()->user();
        $user->reservations();

        // Réservations à venir 
        $reservationsActives = $user->reservations()
            ->with('annonce')
            ->whereDate('end_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc')
            ->get();

        // Réservations passées
        $reservationsPassees = $user->reservations()
            ->with('annonce')
            ->whereDate('end_date', '<', Carbon::today())
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($reservation) use ($user) {
                $hasCommented = Comment::where('user_id', $user->id)
                    ->where('annonce_id', $reservation->annonce_id)
                    ->exists();

                // transforme annonce en tableau pour injecter une propriété custom
                $annonce = $reservation->annonce->toArray();
                $annonce['has_commented'] = $hasCommented;

                $reservation->annonce = $annonce;

                return $reservation;
            });

        return Inertia::render('Profile/Reservations', [
            'reservationsActives' => $reservationsActives,
            'reservationsPassees' => $reservationsPassees,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Annonce $annonce)
    {
        return Inertia::render('Reservations/Create', [
            'annonce' => $annonce,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $annonceId)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $start = Carbon::createFromFormat('Y-m-d', substr($request->start_date, 0, 10))->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', substr($request->end_date, 0, 10))->endOfDay();

        $annonce = Annonce::findOrFail($annonceId);
        
        // on peut pas reserver une annonce si on est le propriétaire
        if ($annonce->user_id === auth()->id()) {
            abort(403, "Vous ne pouvez pas réserver votre propre annonce.");
        }

        //  Vérifier que toute la période est couverte par une dispo existante
        $dispo = Calendrier::where('annonce_id', $annonce->id)
            ->where('type', 'disponible')
            ->where('start_date', '<=', $start)
            ->where('end_date', '>=', $end)
            ->first();

        if (!$dispo) {
            throw ValidationException::withMessages([
                'start_date' => 'Cette période n’est pas disponible à la réservation.',
            ]);
        }

        // c pour mieux calculer les dates meme si c le même jour (par exemple du 5 mai au 6 mai ➔ il dira 1 jour, alors qu'on veut 1 nuit) et il faut forcer au moins 1 nuit pour louer.
        $days = max(1, $start->diffInDays($end));
        // calcul du prix
        $price = $days * $annonce->price_per_night;

        // Bloquer la période dans le calendrier
        $calendrier = Calendrier::create([
            'annonce_id' => $annonce->id,
            'start_date' => $start,
            'end_date' => $end,
            'type' => 'réservé',
        ]);

        // Créer la réservation
        $reservation = Reservation::create([
            'annonce_id' => $annonce->id,
            'user_id' => auth()->id(),
            'start_date' => $start,
            'end_date' => $end,
            'price' => $price,
            'calendrier_id' => $calendrier->id,
        ]);

        // Découper les disponibilités si chevauchement
        $dispo = Calendrier::where('annonce_id', $annonce->id)
            ->where('type', 'disponible')
            ->where('start_date', '<=', $start)
            ->where('end_date', '>=', $end)
            ->first();

        if ($dispo) {
            $dispo->delete();

            if ($start->greaterThan($dispo->start_date)) {
                Calendrier::create([
                    'annonce_id' => $annonce->id,
                    'start_date' => $dispo->start_date,
                    'end_date' => $start,
                    'type' => 'disponible',
                ]);
            }

            if ($end->lessThan($dispo->end_date)) {
                Calendrier::create([
                    'annonce_id' => $annonce->id,
                    'start_date' => $end,
                    'end_date' => $dispo->end_date,
                    'type' => 'disponible',
                ]);
            }
        }

        return redirect()->route('reservations.confirmation', ['reservation' => $reservation->id]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        //
    }
}
