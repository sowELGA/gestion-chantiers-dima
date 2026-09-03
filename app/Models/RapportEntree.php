<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportEntree extends Model
{
    protected $table = 'rapports_entrees';

    protected $fillable = [
        'quantite_recue',
        'date_reception',
        'observation',
        'demande_id',
        'chantier_id',
        'receptionnee_par_id',
    ];

    protected $casts = [
        'date_reception' => 'date',
        'quantite_recue' => 'decimal:2',
    ];

    public function demande(): BelongsTo
    {
        return $this->belongsTo(Approvisionnement::class, 'demande_id');
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function receptionneePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receptionnee_par_id');
    }
}
