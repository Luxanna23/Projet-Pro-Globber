<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\VisitedCountries;
use App\Models\User;
class ScratchmapController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        // On récupère tous les country_codes uniques des réservations confirmées
        // $visitedCountries = $user->reservations()
        //     ->where('status', 'accepted')
        //     ->with('annonce') // pour avoir accès à annonce->country_code
        //     ->get()
        //     ->pluck('annonce.country_code')
        //     ->unique()
        //     ->filter()
        //     ->values();

        $visitedCountries = $user->visitedCountries()
            ->pluck('country_code')
            ->unique()
            ->filter()
            ->values();
        
        return Inertia::render('Profile/ScratchMap', [
            'visitedCountries' => $visitedCountries,
        ]);
    }

    public function share()
    {
        $user = Auth::user();

        $visited = $user->reservations()
            ->where('status', 'accepted')
            ->with('annonce')
            ->get()
            ->pluck('annonce.country')
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        return Inertia::render('Profile/ScratchMapShare', [
            'visitedCountries' => $visited,
            'userName' => $user->name,
            'total' => count($visited),
        ]);
    }
}
