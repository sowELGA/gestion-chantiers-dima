<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapportsChantier extends Model
{
    use HasFactory;

    protected $table = 'rapports_chantiers';

    protected $fillable = [
        'date_rapport',
        'contenu',
        'chantier_id',
        'auteur_id',
    ];

    protected $casts = [
        'date_rapport' => 'date',
    ];

    // Relations
    public function chantier()
    {
        return $this->belongsTo(Chantier::class, 'chantier_id');
    }

    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }
}
