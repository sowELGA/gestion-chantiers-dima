<?php

namespace App\Models;

use App\Helpers\ApprovisionnementHelper;
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

    /**
     * Délègue à ApprovisionnementHelper::quantiteRestante() — c'était
     * auparavant une seconde implémentation de la même formule, qui en
     * plus ne réutilisait jamais la relation rapportsEntrees déjà chargée
     * (une requête à chaque accès, y compris dans des boucles).
     */
    public function getQuantiteRestanteAttribute(): float
    {
        return ApprovisionnementHelper::quantiteRestante($this);
    }

    // Accesseur "statut" nécessaire pour groupBy('statut') sur une
    // collection Eloquent (DirectionApproController::historique()) :
    // Collection::groupBy() résout la clé via un accès propriété, qui
    // passe par les accesseurs Eloquent — "statutAppro" seul ne suffit
    // pas car ce n'est pas le nom conventionnel attendu ailleurs.
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
