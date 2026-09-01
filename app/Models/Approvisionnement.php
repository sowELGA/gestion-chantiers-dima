<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approvisionnement extends Model
{
    protected $table = 'approvisionnements';

    protected $fillable = [
        'designation',
        'quantite_demandee',
        'unite',
        'priorite',
        'statutAppro',
        'date_livraison_souhaitee',
        'date_commande',
        'date_livraison_prevue',
        'chantier_id',
        'demandeur_id',
    ];

    protected $casts = [
        'date_commande'         => 'date',
        'date_livraison_souhaitee' => 'date',
        'date_livraison_prevue' => 'date',
        'quantite_demandee'     => 'decimal:2',
    ];

    // Accesseur quantité restante
    public function getQuantiteRestanteAttribute(): float
    {
        $totalRecu = $this->rapportsEntrees()->sum('quantite_recue');
        return max(0, $this->quantite_demandee - $totalRecu);
    }

    public function getStatutAttribute()
    {
        return $this->attributes['statutAppro'];
    }

    // Relations
    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function rapportsEntrees()
    {
        return $this->hasMany(RapportEntree::class, 'demande_id');
    }
}
