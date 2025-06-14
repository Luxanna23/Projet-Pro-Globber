<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;

class ReservationExportController extends Controller
{
    public function byEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $reservations = $user->reservations()->with('annonce')->get()->map(function ($r) {
            return [
                'country' => $r->annonce->country,
                'city' => $r->annonce->city,
                'start_date' => $r->start_date,
                'end_date' => $r->end_date,
                'logement_id' => $r->annonce->id,
            ];
        });

        return response()->json($reservations);
    }
}