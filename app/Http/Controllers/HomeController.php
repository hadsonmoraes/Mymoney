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
    public function index()
    {

        $user = auth()->user();

        $contas = $user->conta;
        return view('home', ['contas' => $contas]);
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

        return redirect('/');
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

        return redirect('/');
    }



    public function destroy($id)
    {
        Conta::findOrFail($id)->delete();
        return redirect('/');
    }
}
