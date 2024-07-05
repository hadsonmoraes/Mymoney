<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ContaRequest;
use Carbon\Carbon;
use Exception;

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

        $contas = Conta::where('user_id', $user->id)
            ->when($request->has('name'), function ($whenQuery) use ($request) {
                $whenQuery->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->filled('data_inicio'), function ($whenQuery) use ($request) {
                $whenQuery->where('maturity', '>=', Carbon::parse($request->data_inicio)->format('Y-m-d'));
            })
            ->when($request->filled('data_fim'), function ($whenQuery) use ($request) {
                $whenQuery->where('maturity', '<=', Carbon::parse($request->data_fim)->format('Y-m-d'));
            })
            ->orderByDesc('created_at')
            ->paginate(10)
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

    public function store(ContaRequest $request)
    {
        $request->validated();

        try {

            $contas = new Conta;
            $contas->name = $request->name;
            $contas->value = $request->value;
            $contas->maturity = $request->maturity;
            $contas->situation = $request->situation;
            $contas->note = $request->note;

            $user = Auth::user();
            $contas->user_id = $user->id;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $requestImage = $request->image;

                $extension = $requestImage->extension();

                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

                $requestImage->move(public_path('img/comprovantes' . $contas->user_id), $imageName);

                $contas->image =  $imageName;
            }

            $contas->save();

            return redirect('home')->with('success', 'Conta criada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Conta não Cadastrada');
        }
    }

    public function show($id)
    {


        $contas = Conta::findOrFail($id);

        return view('contas.show', ['contas' => $contas]);
    }

    public function edit($id)
    {

        $contas = Conta::findOrFail($id);

        return view('contas.edit', ['contas' => $contas]);
    }

    public function update(ContaRequest $request)
    {
        try {
            $user_id = auth()->user()->id;
            $data = $request->all();
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $requestImage = $request->image;

                $extension = $requestImage->extension();

                $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

                $requestImage->move(public_path('img/comprovantes' . $user_id), $imageName);

                $data['image'] =  $imageName;
            }
            $id = $request->id;
            Conta::findOrFail($id)->update($data);

            return redirect('home')->with('success', 'Conta atualizada com sucesso!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Conta não atualizada');
        }
    }

    public function destroy($id)
    {
        Conta::findOrFail($id)->delete();
        return redirect('home')->with('success', 'Conta apagada!');
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $contas = Conta::where('user_id', $user->id)
            ->when($request->filled('data_inicio'), function ($query) use ($request) {
                $query->where('maturity', '>=', Carbon::parse($request->data_inicio)->format('Y-m-d'));
            })
            ->when($request->filled('data_fim'), function ($query) use ($request) {
                $query->where('maturity', '<=', Carbon::parse($request->data_fim)->format('Y-m-d'));
            });

        $allContas = $contas->get();

        $contasPagasValor = $allContas->where('situation', 'paid')->sum('value');
        $contasPagasQuantidade = $allContas->where('situation', 'paid')->count();

        $contasPendentes = $allContas->where('situation', 'pending');
        $contasPendentesValor = $contasPendentes->sum('value');
        $contasPendentesQuantidade = $contasPendentes->count();

        $contasCanceladasValor = $allContas->where('situation', 'canceled')->sum('value');
        $contasCanceladasQuantidade = $allContas->where('situation', 'canceled')->count();

        $total = $allContas->sum('value');
        $totalquantidade = $allContas->count();

        return view('dashboard', [
            'contasPagasValor' => $contasPagasValor,
            'contasPagasQuantidade' => $contasPagasQuantidade,
            'contasPendentesValor' => $contasPendentesValor,
            'contasPendentesQuantidade' => $contasPendentesQuantidade,
            'contasCanceladasValor' => $contasCanceladasValor,
            'contasCanceladasQuantidade' => $contasCanceladasQuantidade,
            'total' => $total,
            'totalquantidade' => $totalquantidade,
            'data_inicio' => $request->data_inicio,
            'data_fim' => $request->data_fim
        ]);
    }
}
