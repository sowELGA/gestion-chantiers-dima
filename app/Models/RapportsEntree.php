<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapportsEntree extends Model
{
    use HasFactory;

    protected $table = 'rapports_entrees';

    protected $fillable = [
        'quantite_commandee',
        'quantite_totale_recue',
        'quantite_recue',
        'quantite_restante',
        'date_reception',
        'observation',
        'demande_id',
        'chantier_id',
        'receptionnee_par_id',
    ];

    protected $casts = [
        'date_reception'        => 'date',
        'quantite_commandee'    => 'decimal:2',
        'quantite_totale_recue' => 'decimal:2',
        'quantite_recue'        => 'decimal:2',
        'quantite_restante'     => 'decimal:2',
    ];

    // Relations
    public function demande()
    {
        return $this->belongsTo(Approvisionnement::class, 'demande_id');
    }

    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function receptionneePar()
    {
        return $this->belongsTo(User::class, 'receptionnee_par_id');
    }
}
