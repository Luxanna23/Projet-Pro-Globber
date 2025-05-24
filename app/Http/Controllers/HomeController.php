<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Annonce;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $destinations = Annonce::select('city', DB::raw('COUNT(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        return Inertia::render('Welcome', [
            'destinations' => $destinations,
        ]);
    }
}
