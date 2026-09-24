<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Services\InstallmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request, InstallmentService $installmentService)
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

        // --- Visão de Futuro / Próximos 30 dias ---
        $today = Carbon::today();
        $next30Days = (clone $today)->addDays(30);

        $futurePendingContas = Conta::where('user_id', $user->id)
            ->where('situation', 'pending')
            ->whereDate('maturity', '>=', $today)
            ->whereDate('maturity', '<=', $next30Days)
            ->orderBy('maturity')
            ->get();

        $futureEntradasValor = $futurePendingContas->where('type', 'entrada')->sum('value');
        $futureSaidasValor = $futurePendingContas->where('type', 'saida')->sum('value');
        $futureSaldoProjetado = $futureEntradasValor - $futureSaidasValor;

        // Vencidos pendentes
        $overdueContas = Conta::where('user_id', $user->id)
            ->where('situation', '!=', 'paid')
            ->whereDate('maturity', '<', $today)
            ->orderBy('maturity')
            ->get();
        $overdueValor = $overdueContas->sum('value');
        $overdueCount = $overdueContas->count();

        // Vencendo hoje
        $dueTodayContas = Conta::where('user_id', $user->id)
            ->where('situation', '!=', 'paid')
            ->whereDate('maturity', '=', $today)
            ->get();

        // Vencendo nos próximos 7 dias (excluindo hoje)
        $next7Days = (clone $today)->addDays(7);
        $upcoming7DaysContas = Conta::where('user_id', $user->id)
            ->where('situation', '!=', 'paid')
            ->whereDate('maturity', '>', $today)
            ->whereDate('maturity', '<=', $next7Days)
            ->orderBy('maturity')
            ->get();

        // Parcelamentos ativos com progresso estruturado
        $activeInstallments = $installmentService->getActiveInstallmentsForUser($user->id);

        return view('dashboard', [
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
            'data_fim' => $dataFim,
            // Futuro & Compromissos
            'futureEntradasValor' => $futureEntradasValor,
            'futureSaidasValor' => $futureSaidasValor,
            'futureSaldoProjetado' => $futureSaldoProjetado,
            'overdueContas' => $overdueContas,
            'overdueValor' => $overdueValor,
            'overdueCount' => $overdueCount,
            'dueTodayContas' => $dueTodayContas,
            'upcoming7DaysContas' => $upcoming7DaysContas,
            'activeInstallments' => $activeInstallments,
        ]);
    }
}
