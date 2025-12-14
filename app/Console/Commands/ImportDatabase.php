<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ImportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import {file=db_2025_12_14_sanitized.sql : Ruta del archivo SQL (relativa o absoluta)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa un dump SQL usando las credenciales de .env';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        $path = $this->makeAbsolutePath($file);

        if (! is_file($path)) {
            $this->error("No encuentro el archivo: {$path}");
            return 1;
        }

        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', '3306');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD', '');
        $db   = env('DB_DATABASE');

        if (! $db || ! $user) {
            $this->error('Faltan DB_DATABASE o DB_USERNAME en el .env');
            return 1;
        }

        $mysqlPath = trim(shell_exec('command -v mysql') ?? '');
        if ($mysqlPath === '') {
            $this->error('No se encontró el binario mysql en el PATH.');
            return 1;
        }

        $cmd = sprintf(
            '%s -h%s -P%s -u%s %s < %s',
            escapeshellcmd($mysqlPath),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($db),
            escapeshellarg($path)
        );

        $this->info("Importando {$path} en {$db} ({$host}:{$port})...");

        $process = Process::fromShellCommandline($cmd, base_path(), [
            'MYSQL_PWD' => $pass,
        ]);
        $process->setTimeout(null); // importes grandes

        $process->run(function ($type, $buffer) {
            if ($type === Process::ERR) {
                $this->error(trim($buffer));
            }
        });

        if (! $process->isSuccessful()) {
            $this->error('Fallo la importación.');
            return 1;
        }

        $this->info('Importación completada.');
        return 0;
    }

    protected function makeAbsolutePath(string $file): string
    {
        if (strpos($file, DIRECTORY_SEPARATOR) === 0) {
            return $file;
        }

        return base_path($file);
    }
}
