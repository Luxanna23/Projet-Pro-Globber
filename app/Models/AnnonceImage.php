<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnonceImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'path',
        'annonce_id',
    ];

    /**
     * Relation des images avec l'annonce 
     */
    public function annonce()
    {
        return $this->belongsTo(Annonce::class, 'annonce_id', 'id');
    }
}
