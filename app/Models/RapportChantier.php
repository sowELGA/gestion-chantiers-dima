<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapportChantier extends Model
{
    use HasFactory;

    protected $table = 'rapports_chantiers';

    protected $fillable = [
        'date_rapport',
        'titre',
        'type',
        'contenu',
        'chantier_id',
        'auteur_id',
    ];

    protected $casts = [
        'date_rapport' => 'date',
    ];

    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'avancement' => 'Avancement',
            'incident'   => 'Incident',
            'livraison'  => 'Livraison',
            'reunion'    => 'Réunion',
            default      => 'Autre',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'avancement' => 'bg-blue-100 text-blue-700',
            'incident'   => 'bg-red-100 text-red-600',
            'livraison'  => 'bg-amber-100 text-amber-700',
            'reunion'    => 'bg-purple-100 text-purple-700',
            default      => 'bg-slate-100 text-slate-600',
        };
    }
}
