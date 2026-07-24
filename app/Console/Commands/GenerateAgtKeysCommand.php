<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AgtSignatureService;
use Exception;

class GenerateAgtKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agt:generate-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera o par de chaves RSA (1024 bits) para a Faturação Eletrónica AGT.';

    /**
     * Execute the console command.
     */
    public function handle(AgtSignatureService $signatureService)
    {
        $this->info('A iniciar a geração das chaves RSA de 1024 bits...');

        try {
            $keys = $signatureService->generateKeyPair();
            
            $this->info('Chaves geradas com sucesso e armazenadas em storage/app/agt_keys/');
            $this->line('');
            $this->info('--- CHAVE PÚBLICA (Para enviar no Modelo 8) ---');
            $this->line($keys['public_key']);
            
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Ocorreu um erro: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
