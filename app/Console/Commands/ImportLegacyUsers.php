<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportLegacyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import-legacy {file=ms_users_2025_12_14/old_users.csv : Ruta del CSV con usuarios legados} {--chunk=500 : Tamaño del lote para inserción}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa usuarios legados desde un CSV (estructura users_) a la tabla users';

    public function handle(): int
    {
        $path = $this->argument('file');
        $chunkSize = (int) $this->option('chunk');

        $fullPath = $this->makeAbsolutePath($path);
        if (! is_file($fullPath)) {
            $this->error("No encuentro el archivo: {$fullPath}");
            return 1;
        }

        $this->info("Leyendo CSV: {$fullPath}");

        $rows = $this->readCsv($fullPath);
        if ($rows->isEmpty()) {
            $this->warn('No se encontraron filas para importar.');
            return 0;
        }

        $this->info("Importando {$rows->count()} usuarios...");

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $rows->chunk($chunkSize)->each(function (Collection $chunk) {
            DB::table('users')->upsert($chunk->all(), ['id'], array_keys($chunk->first()));
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Importación completada.');
        return 0;
    }

    protected function readCsv(string $path): Collection
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            $this->error('No se pudo abrir el archivo CSV.');
            return collect();
        }

        $header = fgetcsv($handle);
        if (! $header) {
            $this->error('El CSV no tiene encabezados.');
            fclose($handle);
            return collect();
        }

        $rows = collect();
        while (($data = fgetcsv($handle)) !== false) {
            $row = array_combine($header, $data);
            if (! $row) {
                continue;
            }

            // Normaliza valores vacíos a null.
            $normalized = collect($row)->map(function ($value) {
                return $value === '' ? null : $value;
            });

            $password = $normalized['password'];
            if ($password === null) {
                // Genera un password temporal si viene nulo para cumplir NOT NULL.
                $password = Hash::make(Str::random(24));
            }

            $rows->push([
                'id' => (int) $normalized['id'],
                'name' => $normalized['name'],
                'role_id' => $normalized['role_id'],
                'daily_goal' => $normalized['daily_goal'],
                'email' => $normalized['email'],
                'status_id' => $normalized['status_id'] ?? 1,
                'document' => $normalized['document'],
                'address' => $normalized['address'],
                'birth_date' => $normalized['birth_date'],
                'hourly_rate' => $normalized['hourly_rate'],
                'password' => $password,
                'remember_token' => $normalized['remember_token'],
                'color' => $normalized['color'],
                'availability' => $normalized['availability'],
                'enterprise_id' => $normalized['enterprise_id'],
                'facebook_id' => $normalized['facebook_id'],
                'phone' => $normalized['phone'],
                'image_url' => $normalized['image_url'],
                'position' => $normalized['position'],
                'created_at' => $normalized['created_at'],
                'updated_at' => $normalized['updated_at'],
                'entry_date' => $normalized['entry_date'],
                'termination_date' => $normalized['termination_date'],
                'contracted_hours' => $normalized['contracted_hours'],
                'contract_type' => $normalized['contract_type'],
                'blood_type' => $normalized['blood_type'],
                'last_login' => $normalized['last_login'],
                'arl' => $normalized['arl'],
                'eps' => $normalized['eps'],
                // Campos del esquema actual para compatibilidad
                'email_verified_at' => null,
            ]);
        }

        fclose($handle);

        return $rows;
    }

    protected function makeAbsolutePath(string $file): string
    {
        return str_starts_with($file, DIRECTORY_SEPARATOR)
            ? $file
            : base_path($file);
    }
}
