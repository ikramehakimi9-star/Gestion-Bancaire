<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompteRequest;
use App\Models\Client;
use App\Models\Compte;
use Illuminate\Http\Request;

class CompteWebController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $comptes = Compte::with('client')
            ->when($q !== '', function ($query) use ($q) {
                $like    = '%' . addcslashes($q, '%_\\') . '%';
                $ribLike = '%' . addcslashes(preg_replace('/\s+/', '', $q), '%_\\') . '%';

                $query->where(function ($sub) use ($like, $ribLike) {
                    $sub->where('rib', 'like', $ribLike)
                        ->orWhereHas('client', function ($c) use ($like) {
                            $c->where('nom', 'like', $like)
                              ->orWhere('prenom', 'like', $like)
                              ->orWhere('cin', 'like', $like);
                        });
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        if ($comptes->isEmpty() && $comptes->currentPage() > 1) {
            return redirect()->to($comptes->url($comptes->lastPage()));
        }

        session(['retour_comptes' => $request->fullUrl()]);

        return view('comptes.index', compact('comptes', 'q'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();

        return view('comptes.create', compact('clients'));
    }

    public function store(CompteRequest $request)
    {
        $data = $request->validated();
        $data['solde'] = $data['solde'] ?? 0;

        $compte = Compte::create($data);
        $compte->load('client');

        return redirect()->route('comptes.index')
            ->with('success', "Le compte {$compte->rib_formate} a été ajouté pour {$compte->client->prenom} {$compte->client->nom}.");
    }

    public function show(Compte $compte)
    {
        $compte->load([
            'client',
            'virementsEmis.destinataire.client',
            'virementsRecus.emetteur.client',
        ]);

        return view('comptes.show', compact('compte'));
    }

    public function edit(Compte $compte)
    {
        $clients = Client::orderBy('nom')->get();

        return view('comptes.edit', compact('compte', 'clients'));
    }

    public function update(CompteRequest $request, Compte $compte)
    {
        $data = $request->validated();
        $data['solde'] = $data['solde'] ?? $compte->solde;

        $compte->update($data);

        return redirect($this->retour())
            ->with('success', "Le compte {$compte->rib_formate} a été mis à jour.");
    }

    public function destroy(Compte $compte)
    {
        $libelle = $compte->rib_formate;

        $compte->delete();

        return redirect($this->retour())
            ->with('success', "Le compte {$libelle} a été supprimé.");
    }

    private function retour(): string
    {
        return session('retour_comptes', route('comptes.index'));
    }
}
