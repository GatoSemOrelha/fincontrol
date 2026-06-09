<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

/**
 * Controller: ClientController
 */
class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::where('user_id', $request->user()->id)
            ->withCount('transactions')
            ->orderBy('name')
            ->get();

        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
        ], [
            'name.required' => 'O nome do cliente é obrigatório.',
        ]);

        $data['user_id'] = $request->user()->id;
        Client::create($data);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function update(Request $request, Client $client)
    {
        if ($client->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
        ]);

        $client->update($data);

        return redirect()->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Request $request, Client $client)
    {
        if ($client->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        if ($client->transactions()->exists()) {
            return back()->with('error', 'Não é possível excluir um cliente com lançamentos vinculados.');
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}
