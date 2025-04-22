<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Http\Requests\StoreAnnonceRequest;
use App\Http\Requests\UpdateAnnonceRequest;
use App\Models\AnnonceImage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$annonces = Annonce::all();
        $annonces = Annonce::with('images')->get(); 
        return inertia::render('Annonces/Index', [
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|digits_between:4,10',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $annonce = Annonce::create([
            'title' => $request->title,
            'description' => $request->description,
            'price_per_night' => $request->price_per_night,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'city' => $request->city,
            'country' => $request->country,
            'user_id' => auth()->id(),
        ]);
    
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
        return Inertia::render('Annonces/Show', [
            'annonce' => [
                ...$annonce->toArray(),
                'image_urls' => $annonce->images->map(fn ($image) => asset('storage/AnnonceImage/'.$image->path)),
            ],
            'user' => auth()->user(),
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
