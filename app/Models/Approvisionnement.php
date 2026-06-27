<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approvisionnement extends Model
{
    use HasFactory;

    protected $table = 'approvisionnements';

    protected $fillable = [
        'designation',
        'quantite_demandee',
        'unite',
        'priorite',
        'statut',
        'date_commande',
        'date_livraison_prevue',
        'chantier_id',
        'demandeur_id',
    ];

    protected $casts = [
        'date_commande'         => 'date',
        'date_livraison_prevue' => 'date',
        'quantite_demandee'     => 'decimal:2',
    ];

    // Accesseur quantité restante globale
    public function getQuantiteRestanteAttribute(): float
    {
        $totalRecu = $this->rapportsEntrees()->sum('quantite_recue');
        return max(0, $this->quantite_demandee - $totalRecu);
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
        return $this->hasMany(RapportsEntree::class, 'demande_id');
    }
}
