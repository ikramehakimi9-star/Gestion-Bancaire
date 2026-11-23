<?php

namespace App\Http\Controllers;

use App\Http\Requests\VirementRequest;
use App\Models\Compte;
use App\Models\Virement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VirementController extends Controller
{
    public function index()
    {
        $virements = Virement::with(['emetteur.client', 'destinataire.client'])
            ->latest()
            ->paginate(10);

        return view('virements.index', compact('virements'));
    }

    public function create()
    {
        $comptes = Compte::with('client')->orderBy('client_id')->get();

        return view('virements.create', compact('comptes'));
    }

    public function store(VirementRequest $request)
    {
        $montant = round((float) $request->montant, 2);

        try {
            $virement = DB::transaction(function () use ($request, $montant) {
                // On verrouille les deux comptes, toujours dans l'ordre des id,
                // pour éviter les blocages si deux virements croisés arrivent en même temps.
                $comptes = Compte::whereIn('id', [
                        $request->compte_emetteur_id,
                        $request->compte_destinataire_id,
                    ])
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $emetteur     = $comptes[$request->compte_emetteur_id];
                $destinataire = $comptes[$request->compte_destinataire_id];

                // Vérification du solde (comparaison en centimes pour éviter les erreurs d'arrondi)
                if (round((float) $emetteur->solde * 100) < round($montant * 100)) {
                    throw new \DomainException('Solde insuffisant : le compte émetteur ne dispose pas du montant demandé.');
                }

                $emetteur->decrement('solde', $montant);
                $destinataire->increment('solde', $montant);

                // On renvoie le virement créé pour pouvoir lire son id plus bas
                return Virement::create([
                    'compte_emetteur_id'     => $emetteur->id,
                    'compte_destinataire_id' => $destinataire->id,
                    'montant'                => $montant,
                ]);
            });
        } catch (\DomainException $e) {
            // Erreur métier : solde insuffisant. La transaction est annulée.
            return back()->withInput()->withErrors(['montant' => $e->getMessage()]);
        } catch (\Throwable $e) {
            // Erreur technique inattendue : on la garde dans les logs
            Log::error('Échec du virement : ' . $e->getMessage());

            return back()->withInput()
                ->withErrors(['montant' => 'Une erreur est survenue. Le virement a été annulé.']);
        }

        return redirect()->route('virements.index')
            ->with('success', 'Virement n° ' . str_pad($virement->id, 6, '0', STR_PAD_LEFT)
                . ' de ' . number_format($montant, 2, ',', ' ') . ' DH effectué avec succès.');
    }
}
