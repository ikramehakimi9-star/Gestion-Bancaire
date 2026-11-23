<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientWebController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $clients = Client::withCount('comptes')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '%_\\') . '%';

                $query->where(function ($sub) use ($like) {
                    $sub->where('nom', 'like', $like)
                        ->orWhere('prenom', 'like', $like)
                        ->orWhere('cin', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('telephone', 'like', $like);
                });
            })
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(10)
            ->withQueryString();

        // Page vide (par exemple après une suppression) : retour à la dernière page existante
        if ($clients->isEmpty() && $clients->currentPage() > 1) {
            return redirect()->to($clients->url($clients->lastPage()));
        }

        // On mémorise la liste (recherche et page) pour y revenir après Voir, Modifier, Supprimer
        session(['retour_clients' => $request->fullUrl()]);

        return view('clients.index', compact('clients', 'q'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        $client = Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', "Le client {$client->prenom} {$client->nom} a été ajouté avec succès.");
    }

    public function show(Client $client)
    {
        $client->load('comptes');

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect($this->retour())
            ->with('success', "Les informations de {$client->prenom} {$client->nom} ont été mises à jour.");
    }

    public function destroy(Client $client)
    {
        $nom       = "{$client->prenom} {$client->nom}";
        $nbComptes = $client->comptes()->count();

        $client->delete(); // ses comptes sont supprimés en cascade

        $detail = $nbComptes > 0
            ? " ({$nbComptes} " . ($nbComptes > 1 ? 'comptes supprimés' : 'compte supprimé') . ')'
            : '';

        return redirect($this->retour())
            ->with('success', "Le client {$nom} a été supprimé{$detail}.");
    }

    private function retour(): string
    {
        return session('retour_clients', route('clients.index'));
    }
}
