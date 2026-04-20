<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectBrief\ProjectBriefRequest;
use App\Models\Project;
use App\Models\ProjectBrief;
use App\Models\ProjectBriefAnswer;
use App\Models\ProjectLogin;
use App\Models\ProjectMetaData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProjectBriefController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store', 'publicEdit', 'publicUpdate']);
        $this->middleware(function (Request $request, $next) {
            $this->authorizeModule($request, '/projects', [
                'index' => 'read',
                'show' => 'read',
                'edit' => 'update',
                'update' => 'update',
                'destroy' => 'delete',
            ]);

            return $next($request);
        })->except(['create', 'store', 'publicEdit', 'publicUpdate']);
    }

    public function index(Project $project): View
    {
        $briefs = $project->briefs()
            ->with('creator:id,name')
            ->withCount('answers')
            ->latest()
            ->paginate(12);

        return view('project_briefs.index', [
            'project' => $project,
            'briefs' => $briefs,
        ]);
    }

    public function create(Project $project): View
    {
        return view('project_briefs.create', [
            'project' => $project,
            'brief' => new ProjectBrief([
                'title' => 'Brief '.$project->name,
            ]),
            'answers' => collect(),
            'questions' => $this->briefQuestions($project),
        ]);
    }

    public function store(ProjectBriefRequest $request, Project $project): RedirectResponse
    {
        $brief = DB::transaction(function () use ($request, $project): ProjectBrief {
            $brief = $project->briefs()->create([
                'created_by' => $request->user()?->id,
                'title' => $request->validated('title'),
                'notes' => $request->validated('notes'),
            ]);

            $this->syncAnswers($brief, $request->validated(), $request);
            $this->syncLogins($brief->project, $request->validated('access_logins', []));

            return $brief;
        });

        if ($request->user()) {
            return redirect()->route('projects.briefs.show', [$project, $brief])->with('status', 'Brief creado.');
        }

        return redirect()->route('public.briefs.edit', $brief->public_token)->with('status', 'Brief creado.');
    }

    public function show(Project $project, ProjectBrief $brief): View
    {
        $this->ensureSameProject($project, $brief);

        $brief->load([
            'creator:id,name',
            'answers.question.parent',
        ]);

        return view('project_briefs.show', [
            'project' => $project,
            'brief' => $brief,
            'answersBySection' => $brief->answers
                ->sortBy(fn (ProjectBriefAnswer $answer) => [
                    $answer->question?->parent_id ?? $answer->question?->id ?? 0,
                    $answer->question?->id ?? 0,
                ])
                ->groupBy(fn (ProjectBriefAnswer $answer) => $answer->question?->parent?->value ?? 'General'),
        ]);
    }

    public function edit(Project $project, ProjectBrief $brief): View
    {
        $this->ensureSameProject($project, $brief);

        $brief->load('answers');

        return view('project_briefs.edit', [
            'project' => $project,
            'brief' => $brief,
            'answers' => $brief->answers->pluck('value', 'project_meta_data_id'),
            'questions' => $this->briefQuestions($project),
        ]);
    }

    public function update(ProjectBriefRequest $request, Project $project, ProjectBrief $brief): RedirectResponse
    {
        $this->ensureSameProject($project, $brief);

        DB::transaction(function () use ($request, $brief): void {
            $brief->update([
                'title' => $request->validated('title'),
                'notes' => $request->validated('notes'),
            ]);

            $this->syncAnswers($brief, $request->validated(), $request);
            $this->syncLogins($brief->project, $request->validated('access_logins', []));
        });

        return redirect()->route('projects.briefs.show', [$project, $brief])->with('status', 'Brief actualizado.');
    }

    public function destroy(Project $project, ProjectBrief $brief): RedirectResponse
    {
        $this->ensureSameProject($project, $brief);

        $brief->delete();

        return redirect()->route('projects.briefs.index', $project)->with('status', 'Brief eliminado.');
    }

    public function publicEdit(ProjectBrief $brief): View
    {
        $brief->load(['project', 'answers']);

        return view('project_briefs.public', [
            'project' => $brief->project,
            'brief' => $brief,
            'answers' => $brief->answers->pluck('value', 'project_meta_data_id'),
            'questions' => $this->briefQuestions($brief->project),
        ]);
    }

    public function publicUpdate(ProjectBriefRequest $request, ProjectBrief $brief): RedirectResponse
    {
        DB::transaction(function () use ($request, $brief): void {
            $brief->update([
                'title' => $brief->title,
                'notes' => $brief->notes,
            ]);

            $this->syncAnswers($brief, $request->validated(), $request);
            $this->syncLogins($brief->project, $request->validated('access_logins', []));
        });

        return redirect()->route('public.briefs.edit', $brief->public_token)->with('status', 'Brief enviado.');
    }

    protected function ensureSameProject(Project $project, ProjectBrief $brief): void
    {
        abort_unless($brief->project_id === $project->id, 404);
    }

    protected function briefQuestions(?Project $project = null): Collection
    {
        $questionQuery = ProjectMetaData::query()
            ->with('children.children.children')
            ->whereNull('parent_id')
            ->orderBy('weight')
            ->orderBy('id');

        if ($project) {
            $rootIds = $this->answeredRootQuestionIds($project);

            if ($rootIds->isEmpty()) {
                $rootIds = $this->campaignRootQuestionIds('Brief Web Design');
            }

            if ($rootIds->isNotEmpty()) {
                $rootIds = $rootIds->merge($this->fixedPublicRootQuestionIds())->unique()->values();
                $questionQuery->whereIn('id', $rootIds);
            }
        }

        return $questionQuery->get();
    }

    /**
     * @param  array{answers?: array<int|string, string|null>, selected_options?: array<int|string, array<int|string, int|string>>}  $data
     */
    protected function syncAnswers(ProjectBrief $brief, array $data, ProjectBriefRequest $request): void
    {
        $answers = [];

        foreach (($data['answers'] ?? []) as $metaDataId => $value) {
            $normalizedValue = trim((string) $value);

            if ($normalizedValue !== '') {
                $answers[(int) $metaDataId] = $normalizedValue;
            }
        }

        foreach (($data['selected_options'] ?? []) as $optionIds) {
            foreach ($optionIds as $optionId) {
                $answers[(int) $optionId] = 'on';
            }
        }

        foreach ($request->file('files', []) as $metaDataId => $file) {
            if ($file && $file->isValid()) {
                $answers[(int) $metaDataId] = json_encode([
                    'path' => $file->store('files/project-briefs/'.$brief->id, 'public'),
                    'name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $brief->answers()
            ->pluck('value', 'project_meta_data_id')
            ->each(function (string $value, int $metaDataId) use (&$answers): void {
                if (array_key_exists($metaDataId, $answers)) {
                    return;
                }

                $fileAnswer = json_decode($value, true);

                if (is_array($fileAnswer) && isset($fileAnswer['path'])) {
                    $answers[$metaDataId] = $value;
                }
            });

        $brief->answers()->delete();

        foreach ($answers as $metaDataId => $value) {
            $brief->answers()->create([
                'project_meta_data_id' => $metaDataId,
                'value' => $value,
            ]);
        }
    }

    protected function answeredRootQuestionIds(Project $project): Collection
    {
        $answeredQuestionIds = DB::table('project_metas')
            ->where('project_id', $project->id)
            ->whereNotNull('value')
            ->whereRaw("NULLIF(TRIM(value), '') IS NOT NULL")
            ->pluck('meta_data_id')
            ->map(fn ($id): int => (int) $id)
            ->filter();

        if ($answeredQuestionIds->isEmpty()) {
            return collect();
        }

        $questions = ProjectMetaData::query()
            ->with('parent.parent.parent')
            ->whereIn('id', $answeredQuestionIds)
            ->get();

        return $questions
            ->map(function (ProjectMetaData $question): int {
                while ($question->parent) {
                    $question = $question->parent;
                }

                return (int) $question->id;
            })
            ->unique()
            ->values();
    }

    protected function fixedPublicRootQuestionIds(): Collection
    {
        return ProjectMetaData::query()
            ->whereNull('parent_id')
            ->where(function ($query): void {
                $query
                    ->where('value', 'like', '%Archivos%')
                    ->orWhere('value', 'like', '%Accesos%');
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id);
    }

    protected function campaignRootQuestionIds(string $campaignName): Collection
    {
        if (! Schema::hasTable('campaign_project_meta_data') || ! Schema::hasTable('campaigns')) {
            return collect();
        }

        $questionIds = DB::table('campaign_project_meta_data')
            ->join('campaigns', 'campaigns.id', '=', 'campaign_project_meta_data.campaign_id')
            ->where('campaigns.name', $campaignName)
            ->pluck('campaign_project_meta_data.project_meta_data_id')
            ->map(fn ($id): int => (int) $id)
            ->filter();

        if ($questionIds->isEmpty()) {
            return collect();
        }

        return ProjectMetaData::query()
            ->with('parent.parent.parent')
            ->whereIn('id', $questionIds)
            ->get()
            ->map(function (ProjectMetaData $question): int {
                while ($question->parent) {
                    $question = $question->parent;
                }

                return (int) $question->id;
            })
            ->unique()
            ->values();
    }

    /**
     * @param  array<int, array{name?: string|null, user?: string|null, password?: string|null, url?: string|null}>  $logins
     */
    protected function syncLogins(Project $project, array $logins): void
    {
        foreach ($logins as $login) {
            $name = trim((string) ($login['name'] ?? ''));
            $user = trim((string) ($login['user'] ?? ''));
            $password = trim((string) ($login['password'] ?? ''));
            $url = trim((string) ($login['url'] ?? ''));

            if ($name === '' && $user === '' && $password === '' && $url === '') {
                continue;
            }

            ProjectLogin::query()->updateOrCreate(
                [
                    'project_id' => $project->id,
                    'name' => $name !== '' ? $name : 'Acceso',
                    'user' => $user,
                    'url' => $url,
                ],
                [
                    'password' => $password,
                ],
            );
        }
    }
}
