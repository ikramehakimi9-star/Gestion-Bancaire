<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Compte;
use App\Models\Virement;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'clients'              => Client::count(),
            'comptes'              => Compte::count(),
            'solde_total'          => (float) Compte::sum('solde'),
            'virements'            => Virement::count(),
            'montant_total'        => (float) Virement::sum('montant'),
            'virements_aujourdhui' => Virement::whereDate('created_at', today())->count(),
        ];

        $derniersVirements = Virement::with(['emetteur.client', 'destinataire.client'])
            ->latest()
            ->take(5)
            ->get();

        $topComptes = Compte::with('client')
            ->orderByDesc('solde')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'derniersVirements', 'topComptes'));
    }
}
