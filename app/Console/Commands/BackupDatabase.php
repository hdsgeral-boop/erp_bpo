<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes a full database backup using pg_dump';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
        $path = storage_path("app/backups/" . $filename);
        
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $command = sprintf(
            'PGPASSWORD="%s" pg_dump -U %s -h %s -p %s %s > %s',
            env('DB_PASSWORD'),
            env('DB_USERNAME'),
            env('DB_HOST'),
            env('DB_PORT', '5432'),
            env('DB_DATABASE'),
            $path
        );

        $returnVar = NULL;
        $output  = NULL;
        
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("Backup salvo com sucesso em: " . $path);
        } else {
            $this->error("Erro ao efetuar o backup.");
        }
    }
}
