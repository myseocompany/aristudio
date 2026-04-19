<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectBrief\ProjectBriefRequest;
use App\Models\Project;
use App\Models\ProjectBrief;
use App\Models\ProjectBriefAnswer;
use App\Models\ProjectMetaData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectBriefController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['publicEdit', 'publicUpdate']);
        $this->middleware(function (Request $request, $next) {
            $this->authorizeModule($request, '/projects', [
                'index' => 'read',
                'create' => 'create',
                'store' => 'create',
                'show' => 'read',
                'edit' => 'update',
                'update' => 'update',
                'destroy' => 'delete',
            ]);

            return $next($request);
        })->except(['publicEdit', 'publicUpdate']);
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
            'questions' => $this->briefQuestions(),
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

            $this->syncAnswers($brief, $request->validated());

            return $brief;
        });

        return redirect()->route('projects.briefs.show', [$project, $brief])->with('status', 'Brief creado.');
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
            'questions' => $this->briefQuestions(),
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

            $this->syncAnswers($brief, $request->validated());
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
            'questions' => $this->briefQuestions(),
        ]);
    }

    public function publicUpdate(ProjectBriefRequest $request, ProjectBrief $brief): RedirectResponse
    {
        DB::transaction(function () use ($request, $brief): void {
            $brief->update([
                'title' => $brief->title,
                'notes' => $brief->notes,
            ]);

            $this->syncAnswers($brief, $request->validated());
        });

        return redirect()->route('public.briefs.edit', $brief->public_token)->with('status', 'Brief enviado.');
    }

    protected function ensureSameProject(Project $project, ProjectBrief $brief): void
    {
        abort_unless($brief->project_id === $project->id, 404);
    }

    protected function briefQuestions(): Collection
    {
        return ProjectMetaData::query()
            ->with('children.children.children')
            ->whereNull('parent_id')
            ->orderBy('weight')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  array{answers?: array<int|string, string|null>, selected_options?: array<int|string, array<int|string, int|string>>}  $data
     */
    protected function syncAnswers(ProjectBrief $brief, array $data): void
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

        $brief->answers()->delete();

        foreach ($answers as $metaDataId => $value) {
            $brief->answers()->create([
                'project_meta_data_id' => $metaDataId,
                'value' => $value,
            ]);
        }
    }
}
