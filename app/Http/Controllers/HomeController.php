<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        $user = auth()->user();

        $contas = Conta::when($request->has('name'), function ($whenQuery) use ($request) {
            $whenQuery->where('name', 'like', '%' . $request->name . '%');
        })
            ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
                $whenQuery->where('maturity', '>=', \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'));
            })
            ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
                $whenQuery->where('maturity', '<=', \Carbon\Carbon::parse($request->data_fim)->format('Y-m-d'));
            })
            ->Where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        return view('home', [
            'contas' => $contas,
            'name' => $request->name,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim
        ]);
    }

    public function create()
    {
        return view('contas.create');
    }

    public function store(Request $request)
    {
        $contas = new Conta;
        $contas->name = $request->name;
        $contas->value = $request->value;
        $contas->maturity = $request->maturity;

        $user = Auth::user();
        $contas->user_id = $user->id;

        $contas->save();

        return redirect('home')->with('status', 'Conta criada com sucesso!');
    }

    public function show($id)
    {


        $contas = Conta::findOrFail($id);

        return view('contas.show', ['contas' => $contas]);
    }

    public function edit($id)
    {

        // $user = auth()->user();
        $contas = Conta::findOrFail($id);

        return view('contas.edit', ['contas' => $contas]);
    }

    public function update(Request $request)
    {

        $data = $request->all();
        $id = $request->id;
        Conta::findOrFail($id)->update($data);

        return redirect('home')->with('status', 'Conta atualizada com sucesso!');
    }



    public function destroy($id)
    {
        Conta::findOrFail($id)->delete();
        return redirect('home')->with('status', 'Conta apagada!');
    }
}
