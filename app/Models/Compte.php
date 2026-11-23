<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Compte extends Model
{
    use HasFactory;

    protected $fillable = ['rib', 'solde', 'client_id'];

    protected $casts = [
        'solde' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function virementsEmis(): HasMany
    {
        return $this->hasMany(Virement::class, 'compte_emetteur_id');
    }

    public function virementsRecus(): HasMany
    {
        return $this->hasMany(Virement::class, 'compte_destinataire_id');
    }

    // RIB groupé par blocs de 4 : 1234 5678 9012 3456 7890 1234
    protected function ribFormate(): Attribute
    {
        return Attribute::get(fn () => trim(chunk_split((string) $this->rib, 4, ' ')));
    }

    // Solde lisible : 12 345,60 DH (espaces insécables pour ne jamais couper la ligne)
    protected function soldeFormate(): Attribute
    {
        return Attribute::get(fn () =>
            number_format((float) $this->solde, 2, ',', "\u{00A0}") . "\u{00A0}DH"
        );
    }

    // Couleur du solde : rouge (vide), orange (inférieur à 1 000 DH), vert (normal)
    protected function soldeClasse(): Attribute
    {
        return Attribute::get(function () {
            $solde = (float) $this->solde;

            if ($solde <= 0) {
                return 'text-danger';
            }

            return $solde < 1000 ? 'text-warning-emphasis' : 'text-success';
        });
    }
}
