<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CommentController extends Controller
{
    public function store(Request $request, Annonce $annonce)
     {
        $request->validate([
            'content' => 'required|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $user = auth()->user();

        $reservation = Reservation::where('user_id', $user->id)
            ->where('annonce_id', $annonce->id)
            ->where('status', 'accepted')
            ->where('end_date', '<=', now())
            ->where('has_commented', false)
            ->first();

        if (! $reservation) {
            abort(403, 'Vous pouvez uniquement laisser un avis si votre voyage est accepté et terminé.');
        }

        Comment::create([
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
            'content' => $request->input('content'),
            'rating' => $request->input('rating'),
        ]);

        $reservation->has_commented = true;
        $reservation->save();

        return Redirect::back()->with('success', 'Merci pour votre avis !');
    }
}