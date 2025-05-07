<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Calendrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'start_date',
        'end_date',
        'annonce_id',
        'type',
    ];

     // Relation vers Annonce
     public function annonce()
     {
         return $this->belongsTo(Annonce::class);
     }
 
     // Relation inverse
     public function reservation()
     {
         return $this->hasOne(Reservation::class);
     }
}
