<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Http\Requests\StoreAnnonceRequest;
use App\Http\Requests\UpdateAnnonceRequest;
use App\Models\AnnonceImage;
use App\Models\Calendrier;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 9;
        $annonces = Annonce::with('images')->latest()->paginate($perPage); 
        if ($request->wantsJson()) {
            return response()->json([
                'annonces' => $annonces,
            ]);
        }

        return inertia('Annonces/Index', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia::render('Annonces/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|digits_between:4,11',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'country_code' => 'required|string|max:2', 
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'disponibilites' => 'nullable|array',
            'disponibilites.*.start' => 'required|date',
            'disponibilites.*.end' => 'required|date|after_or_equal:disponibilites.*.start',
        ]);

        $annonce = Annonce::create([
            'title' => $request->title,
            'description' => $request->description,
            'price_per_night' => $request->price_per_night,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => $request->country,
            'country_code' => $request->country_code,
            'user_id' => auth()->id(),
        ]);

        if (!empty($validated['disponibilites'])) {
            foreach ($validated['disponibilites'] as $periode) {
                Calendrier::create([
                    'annonce_id' => $annonce->id,
                    'start_date' => $periode['start'],
                    'end_date' => $periode['end'],
                    'type' => 'disponible',
                ]);
            }
        }
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('public/AnnonceImage', $filename);

                // Save l'image dans en db
                AnnonceImage::create([
                    'annonce_id' => $annonce->id,
                    'path' => $filename,
                ]);
            }
        }
        return redirect()->route('annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Annonce $annonce)
    {
        $annonce->load('calendrier', 'comments.user');

        $calendrier = $annonce->calendrier->map(fn ($entry) => [
            'start' => $entry->start_date,
            'end' => $entry->end_date,
            'type' => $entry->type,
        ]);
        return Inertia::render('Annonces/Show', [
            'annonce' => [
                ...$annonce->toArray(),
                'image_urls' => $annonce->images->map(fn ($image) => asset('storage/AnnonceImage/'.$image->path)),
                'comments' => $annonce->comments->map(fn ($comment) => [
                'user' => $comment->user->name,
                'rating' => $comment->rating,
                'content' => $comment->content,
                'created_at' => $comment->created_at->diffForHumans(),
            ]),
            ],
            'user' => auth()->user(),
            'calendrier' => $calendrier,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Annonce $annonce)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Annonce $annonce)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Annonce $annonce)
    {
        //
    }
}
