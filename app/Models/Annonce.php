<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'id',
        'title',
        'description',
        'address',
        'postal_code',
        'city',
        'country',
        'country_code', // ISO code pour la scratchmap
        'price_per_night',
        'user_id',
    ];

    protected $appends = ['first_image'];

    /**
     * Relation avec les images de l'annonce
     */
    public function images()
    {
        return $this->hasMany(AnnonceImage::class, 'annonce_id', 'id');
    }

    // pour que je stock l'image dans le storage
    public static function storeImage($file)
    {
        // faut faire une verif pour voir si le fichier est bien une image avant de stocker
        $path = $file->store('public/AnnonceImage');
        return $path;
    }

    public function getFirstImageAttribute()
    {
        $image = $this->images()->first();
        if ($image) {
            return asset('storage/AnnonceImage/' . $image->path);
        }

        return "https://via.placeholder.com/400x300?text=Annonce";
    }
    public function calendrier()
    {
        return $this->hasMany(Calendrier::class);
    }
    public function disponibilites()
    {
        return $this->hasMany(Calendrier::class)->where('type', 'disponible');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
