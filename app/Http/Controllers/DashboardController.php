<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $dataInicio = $request->filled('data_inicio') ? $request->data_inicio : Carbon::now()->startOfMonth()->format('Y-m-d');
        $dataFim = $request->filled('data_fim') ? $request->data_fim : Carbon::now()->endOfMonth()->format('Y-m-d');

        $contas = Conta::where('user_id', $user->id)
            ->where('maturity', '>=', Carbon::parse($dataInicio)->format('Y-m-d'))
            ->where('maturity', '<=', Carbon::parse($dataFim)->format('Y-m-d'));

        $allContas = $contas->get();

        $contasPagasValor = $allContas->where('situation', 'paid')->sum('value');
        $contasPagasQuantidade = $allContas->where('situation', 'paid')->count();

        $contasPendentes = $allContas->where('situation', 'pending');
        $contasPendentesValor = $contasPendentes->sum('value');
        $contasPendentesQuantidade = $contasPendentes->count();

        $contasCanceladasValor = $allContas->where('situation', 'canceled')->sum('value');
        $contasCanceladasQuantidade = $allContas->where('situation', 'canceled')->count();

        $contasEntrada = $allContas->where('type', 'entrada');
        $contasEntradaValor = $contasEntrada->sum('value');
        $contasEntradaQuantidade = $contasEntrada->count();

        $contasSaida = $allContas->where('type', 'saida');
        $contasSaidaValor = $contasSaida->sum('value');
        $contasSaidaQuantidade = $contasSaida->count();

        $MyTotal = ($contasEntradaValor - $contasSaidaValor);


        $total = $allContas->sum('value');
        $totalquantidade = $allContas->count();

        return Inertia::render('Dashboard', [
            'contasPagasValor' => $contasPagasValor,
            'contasPagasQuantidade' => $contasPagasQuantidade,
            'contasPendentesValor' => $contasPendentesValor,
            'contasPendentesQuantidade' => $contasPendentesQuantidade,
            'contasCanceladasValor' => $contasCanceladasValor,
            'contasCanceladasQuantidade' => $contasCanceladasQuantidade,
            'contasEntradaValor' => $contasEntradaValor,
            'contasEntradaQuantidade' => $contasEntradaQuantidade,
            'contasSaidaValor' => $contasSaidaValor,
            'contasSaidaQuantidade' => $contasSaidaQuantidade,
            'MyTotal' => $MyTotal,
            'total' => $total,
            'totalquantidade' => $totalquantidade,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim
        ]);
    }
}
