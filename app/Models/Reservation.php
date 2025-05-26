<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'price',
        'start_date',
        'end_date',
        'user_id',
        'annonce_id',
        'calendrier_id',
        'status',
        'last_read_at',
        'has_commented',
    ];

    // Relation vers Calendrier
    public function calendrier()
    {
        return $this->belongsTo(Calendrier::class);
    }

    // Relation vers Annonce
    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }

    // Relation vers User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    protected static function booted()
{
    static::deleting(function ($reservation) {
        if ($reservation->calendrier) {
            $reservation->calendrier->delete();
        }
    });
}
}
