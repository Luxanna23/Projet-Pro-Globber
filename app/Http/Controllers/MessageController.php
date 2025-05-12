<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;


class MessageController extends Controller
{
    public function index(Reservation $reservation)
    {
        $user = auth()->user();

        if ($reservation->user_id !== $user->id && $reservation->annonce->user_id !== $user->id) {
            abort(403);
        }

        $reservation->load('annonce');

        return Inertia::render('Messages/Chat', [
            'reservation' => $reservation,
            'messages' => $reservation->messages()->with('sender')->latest()->get(),
            'userId' => $user->id,
        ]);
    }

    public function store(Request $request, Reservation $reservation)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $reservation->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return back();
    }

    public function markAsRead(Reservation $reservation)
    {
        $user = auth()->user();

        if ($reservation->user_id !== $user->id && $reservation->annonce->user_id !== $user->id) {
            abort(403);
        }

        $reservation->update(['last_read_at' => now()]);

        return response()->noContent();
    }

}
