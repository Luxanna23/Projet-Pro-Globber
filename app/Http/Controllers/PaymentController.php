<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Mail\ReservationConfirmed;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $annonce = Annonce::findOrFail($request->annonce_id);
        $user = Auth::user();

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $request->price * 100, // en centimes
                    'product_data' => [
                        'name' => "Réservation de " . $annonce->title,
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('annonces.show', $annonce->id),
            'metadata' => [
                'user_id' => $user->id,
                'annonce_id' => $request->annonce_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function checkoutSuccess(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($request->get('session_id'));

        $metadata = $session->metadata;
        $start = Carbon::createFromFormat('Y-m-d', $metadata->start_date)->startOfDay();
        $end = Carbon::createFromFormat('Y-m-d', $metadata->end_date)->endOfDay();

        $annonce = Annonce::findOrFail($metadata->annonce_id);

        // Validations similaires à ton store()
        if ($annonce->user_id === $metadata->user_id) {
            abort(403, "Vous ne pouvez pas réserver votre propre annonce.");
        }

        $dispo = Calendrier::where('annonce_id', $annonce->id)
            ->where('type', 'disponible')
            ->where('start_date', '<=', $start)
            ->where('end_date', '>=', $end)
            ->first();

        if (!$dispo) {
            return Inertia::render('Checkout/Failed', [
                'error' => 'La période sélectionnée n’est plus disponible.'
            ]);
        }

        $days = max(1, $start->diffInDays($end));
        $cleaningFee = 150 ;
        $taxes = 17; 
        $nighttotal = $days * $annonce->price_per_night;
        $price = round($nighttotal + $cleaningFee + $taxes, 2);

        $calendrier = Calendrier::create([
            'annonce_id' => $annonce->id,
            'start_date' => $start,
            'end_date' => $end,
            'type' => 'réservé',
        ]);

        $reservation = Reservation::create([
            'annonce_id' => $annonce->id,
            'user_id' => $metadata->user_id,
            'start_date' => $start,
            'end_date' => $end,
            'price' => $price,
            'calendrier_id' => $calendrier->id,
        ]);

        //Mail::to($reservation->user->email)->send(new ReservationConfirmed($reservation));

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
}
