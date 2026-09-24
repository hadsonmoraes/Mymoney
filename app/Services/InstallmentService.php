<?php

namespace App\Services;

use App\Models\Conta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class InstallmentService
{
    /**
     * Configura manualmente um lançamento como parcelamento estruturado.
     */
    public function configureInstallment(Conta $conta, array $data): Conta
    {
        $number = (int) ($data['installment_number'] ?? 1);
        $total = (int) ($data['installments_total'] ?? 1);

        if ($number < 1 || $total < 1 || $number > $total) {
            throw new InvalidArgumentException('Número da parcela inválido ou maior que o total.');
        }

        return DB::transaction(function () use ($conta, $number, $total, $data) {
            $groupId = $conta->installment_group_id ?: (string) Str::ulid();

            $updateData = [
                'installment_group_id' => $groupId,
                'installment_number' => $number,
                'installments_total' => $total,
            ];

            if (!empty($data['clean_name'])) {
                $updateData['name'] = $data['clean_name'];
            }

            $conta->update($updateData);

            // Se solicitado vincular lançamentos semelhantes (ex: "Cartão - 4/12", "Cartão - 5/12")
            if (!empty($data['link_related'])) {
                $this->linkRelatedInstallments($conta, $groupId, $total);
            }

            return $conta->fresh();
        });
    }

    /**
     * Procura e vincula lançamentos correlatos com mesmo prefixo e padrão "X/Y".
     */
    protected function linkRelatedInstallments(Conta $baseConta, string $groupId, int $total): void
    {
        // Detecta o nome base sem sufixos de parcela
        $cleanBase = trim(preg_replace('/(?:\s*\-\s*)?(?:parcela\s*|\#\s*)?\d{1,3}\s*(?:\/|\s*de\s*)\s*\d{1,3}.*$/i', '', $baseConta->name));

        if (strlen($cleanBase) < 2) {
            return;
        }

        $candidates = Conta::where('user_id', $baseConta->user_id)
            ->where('id', '!=', $baseConta->id)
            ->whereNull('installment_group_id')
            ->where('name', 'like', $cleanBase . '%')
            ->get();

        foreach ($candidates as $candidate) {
            $pattern = $candidate->detectInstallmentPattern();
            if ($pattern && $pattern['total'] === $total) {
                $candidate->update([
                    'installment_group_id' => $groupId,
                    'installment_number' => $pattern['current'],
                    'installments_total' => $total,
                    // Mantém o nome original ou limpo conforme preferência segura
                ]);
            }
        }
    }

    /**
     * Retorna resumo consolidado e cálculos reais de um grupo de parcelamento.
     */
    public function getInstallmentSummary(string $groupId, int $userId): ?array
    {
        $items = Conta::where('user_id', $userId)
            ->where('installment_group_id', $groupId)
            ->orderBy('installment_number')
            ->orderBy('maturity')
            ->get();

        if ($items->isEmpty()) {
            return null;
        }

        $first = $items->first();
        $totalInstallments = $items->max('installments_total') ?: $items->count();

        $paidContas = $items->where('situation', 'paid');
        $paidCount = $paidContas->count();
        $paidValue = $paidContas->sum(fn($c) => (float) $c->value);

        $pendingContas = $items->filter(fn($c) => $c->situation === 'pending' && $c->maturity >= now()->startOfDay());
        $pendingCount = $pendingContas->count();

        $overdueContas = $items->filter(fn($c) => $c->situation !== 'paid' && $c->maturity < now()->startOfDay());
        $overdueCount = $overdueContas->count();

        $remainingCount = max(0, $totalInstallments - $paidCount);
        $totalValue = $items->sum(fn($c) => (float) $c->value);
        $remainingValue = $items->where('situation', '!=', 'paid')->sum(fn($c) => (float) $c->value);

        $progressPercent = $totalInstallments > 0 ? (int) round(($paidCount / $totalInstallments) * 100) : 0;

        return [
            'group_id' => $groupId,
            'name' => $first->name,
            'category' => $first->category?->name ?? 'Sem categoria',
            'type' => $first->type,
            'total_installments' => $totalInstallments,
            'registered_count' => $items->count(),
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'overdue_count' => $overdueCount,
            'remaining_count' => $remainingCount,
            'total_value' => $totalValue,
            'paid_value' => $paidValue,
            'remaining_value' => $remainingValue,
            'progress_percent' => $progressPercent,
            'items' => $items,
        ];
    }

    /**
     * Retorna a lista de parcelamentos ativos do usuário para o Dashboard e listagens.
     */
    public function getActiveInstallmentsForUser(int $userId): Collection
    {
        $groupIds = Conta::where('user_id', $userId)
            ->whereNotNull('installment_group_id')
            ->distinct()
            ->pluck('installment_group_id');

        $summaries = new Collection();

        foreach ($groupIds as $groupId) {
            $summary = $this->getInstallmentSummary($groupId, $userId);
            if ($summary && $summary['remaining_count'] > 0) {
                $summaries->push((object) $summary);
            }
        }

        return $summaries;
    }

    /**
     * Edição coordenada de parcelas de um grupo.
     */
    public function updateSequence(Conta $conta, array $data, string $scope = 'only_this'): int
    {
        if (empty($conta->installment_group_id) || $scope === 'only_this') {
            $conta->update($data);
            return 1;
        }

        return DB::transaction(function () use ($conta, $data, $scope) {
            $query = Conta::where('user_id', $conta->user_id)
                ->where('installment_group_id', $conta->installment_group_id);

            if ($scope === 'this_and_next') {
                if ($conta->installment_number) {
                    $query->where('installment_number', '>=', $conta->installment_number);
                } else {
                    $query->where('maturity', '>=', $conta->maturity);
                }
            }

            $updatableData = $data;
            unset($updatableData['maturity'], $updatableData['image'], $updatableData['installment_number']);

            $affected = $query->update($updatableData);
            $conta->update($data);

            return $affected;
        });
    }

    /**
     * Exclusão coordenada de parcelas de um grupo.
     */
    public function deleteSequence(Conta $conta, string $scope = 'only_this'): int
    {
        if (empty($conta->installment_group_id) || $scope === 'only_this') {
            $conta->delete();
            return 1;
        }

        return DB::transaction(function () use ($conta, $scope) {
            $query = Conta::where('user_id', $conta->user_id)
                ->where('installment_group_id', $conta->installment_group_id);

            if ($scope === 'this_and_next') {
                if ($conta->installment_number) {
                    $query->where('installment_number', '>=', $conta->installment_number);
                } else {
                    $query->where('maturity', '>=', $conta->maturity);
                }
            }

            return $query->delete();
        });
    }

    /**
     * Encontra outros lançamentos que parecem pertencer ao mesmo parcelamento antigo.
     */
    public function findPotentialInstallmentMatches(Conta $conta): Collection
    {
        $pattern = $conta->detectInstallmentPattern();
        if (!$pattern) {
            return new Collection();
        }

        $cleanBase = trim(preg_replace('/(?:\s*\-\s*)?(?:parcela\s*|\#\s*)?\d{1,3}\s*(?:\/|\s*de\s*)\s*\d{1,3}.*$/i', '', $conta->name));
        if (strlen($cleanBase) < 2) {
            return new Collection();
        }

        $candidates = Conta::where('user_id', $conta->user_id)
            ->where('id', '!=', $conta->id)
            ->whereNull('installment_group_id')
            ->where('name', 'like', $cleanBase . '%')
            ->orderBy('maturity')
            ->get();

        return $candidates->filter(function ($c) use ($pattern) {
            $cPattern = $c->detectInstallmentPattern();
            return $cPattern && $cPattern['total'] === $pattern['total'];
        });
    }
}
