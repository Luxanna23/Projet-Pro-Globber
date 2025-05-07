<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
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
            $reservations = Reservation::with('annonce')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
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

        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $annonce = Annonce::findOrFail($annonceId);
        
        // on peut pas reserver une annonce si on est le propriétaire
        if ($annonce->user_id === auth()->id()) {
            abort(403, "Vous ne pouvez pas réserver votre propre annonce.");
        }

        // Vérifier si la période est bloquée dans le calendrier
        $conflit = Calendrier::where('annonce_id', $annonce->id)
            ->whereIn('type', ['réservé'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                          ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($conflit) {
            throw ValidationException::withMessages([
                'start_date' => 'Cette période n’est pas disponible pour cette annonce.',
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
        Reservation::create([
            'annonce_id' => $annonce->id,
            'user_id' => auth()->id(),
            'start_date' => $start,
            'end_date' => $end,
            'price' => $price,
            'calendrier_id' => $calendrier->id,
        ]);

        return redirect()->back()->with('success', 'Réservation effectuée avec succès.');
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
