<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\RecurrenceService;
use Illuminate\Console\Command;

class ProcessRecurrences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finance:process-recurrences {--date= : Data de referência para execução (Y-m-d)} {--user= : ID de usuário específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processa as regras de recorrências ativas e gera os lançamentos devidos.';

    /**
     * Execute the console command.
     */
    public function handle(RecurrenceService $recurrenceService): int
    {
        $targetDate = $this->option('date') ?: now()->toDateString();
        $userId = $this->option('user');

        $this->info("Iniciando processamento de recorrências até a data {$targetDate}...");

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("Usuário ID {$userId} não encontrado.");
                return Command::FAILURE;
            }

            $count = $recurrenceService->processForUser($user, $targetDate);
            $this->info("Processamento concluído para o usuário {$user->name}. {$count} recorrência(s) processada(s).");
            return Command::SUCCESS;
        }

        $count = $recurrenceService->processDueRecurrences($targetDate);
        $this->info("Processamento concluído. {$count} recorrência(s) processada(s).");

        return Command::SUCCESS;
    }
}
