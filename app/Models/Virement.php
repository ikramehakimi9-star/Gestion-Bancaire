<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Virement extends Model
{
    use HasFactory;

    protected $fillable = [
        'compte_emetteur_id',
        'compte_destinataire_id',
        'montant',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    public function emetteur(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_emetteur_id');
    }

    public function destinataire(): BelongsTo
    {
        return $this->belongsTo(Compte::class, 'compte_destinataire_id');
    }
}
