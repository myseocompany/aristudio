<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $answersTableAlreadyExists = Schema::hasTable('project_brief_answers');

        if (! $answersTableAlreadyExists) {
            Schema::create('project_brief_answers', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('project_brief_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('project_meta_data_id')->index();
                $table->text('value')->nullable();
                $table->timestamps();

                $table->unique(['project_brief_id', 'project_meta_data_id'], 'brief_answer_unique');
            });
        } else {
            Schema::table('project_brief_answers', function (Blueprint $table): void {
                $table->unique(['project_brief_id', 'project_meta_data_id'], 'brief_answer_unique');
            });
        }

        if (Schema::hasTable('project_metas')) {
            DB::table('project_metas')
                ->select('project_id', DB::raw('MAX(updated_at) as last_answer_at'))
                ->whereNotNull('project_id')
                ->groupBy('project_id')
                ->orderBy('project_id')
                ->get()
                ->each(function ($legacyProject): void {
                    $timestamp = $legacyProject->last_answer_at ?: now();
                    $briefId = DB::table('project_briefs')->insertGetId([
                        'project_id' => $legacyProject->project_id,
                        'created_by' => null,
                        'title' => 'Brief legado',
                        'notes' => 'Importado desde las respuestas anteriores del proyecto.',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);

                    DB::table('project_metas')
                        ->where('project_id', $legacyProject->project_id)
                        ->whereNotNull('meta_data_id')
                        ->orderBy('id')
                        ->get()
                        ->each(function ($legacyAnswer) use ($briefId): void {
                            DB::table('project_brief_answers')->insertOrIgnore([
                                'project_brief_id' => $briefId,
                                'project_meta_data_id' => $legacyAnswer->meta_data_id,
                                'value' => $legacyAnswer->value,
                                'created_at' => $legacyAnswer->created_at ?: now(),
                                'updated_at' => $legacyAnswer->updated_at ?: now(),
                            ]);
                        });
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_brief_answers');
    }
};
