@php use Illuminate\Support\Str; @endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background-color: #6439FF;
            color: white;
            padding: 10px 20px;
        }
        .content {
            padding: 20px;
        }
    </style>
    <title>Confirmation de réservation</title>
</head>
<body>
    <div class="header">
        <h1>Confirmation de réservation</h1>
    </div>

    <div class="content">
        <p>Bonjour {{ $reservation->user->name }}</p>

        <p>Nous avons le plaisir de vous informer que votre réservation du logement <strong>{{ Str::limit($reservation->annonce->title, 60) }}</strong> a bien été confirmée.</p>

        <p><strong>📅 Dates :</strong> du {{ \Carbon\Carbon::parse($reservation->start_date)->format('d/m/Y') }}
        au {{ \Carbon\Carbon::parse($reservation->end_date)->format('d/m/Y') }}</p>

        <p><strong>💰 Total :</strong> {{ number_format($reservation->price, 2, ',', ' ') }} €</p>

        <p>Merci pour votre confiance ! </p>
        <p>L'equipe Globber.</p>
    </div>
</body>
</html>