<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::orderBy('nom')->paginate(10));
    }

    public function store(ClientRequest $request)
    {
        $client = Client::create($request->validated());

        return response()->json($client, 201);
    }

    public function show(Client $client)
    {
        return response()->json($client->load('comptes'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return response()->json($client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, 204);
    }
}
