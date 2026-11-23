<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'cin',
        'telephone',
        'email',
        'adresse'
    ];

    public function comptes(): HasMany
    {
        return $this->hasMany(Compte::class);
    }
}
