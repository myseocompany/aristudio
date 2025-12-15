<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectLogin\ProjectLoginIndexRequest;
use App\Models\Project;
use App\Models\ProjectLogin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(ProjectLoginIndexRequest $request): View
    {
        $filters = $request->validated();
        $canSeeAll = $this->canSeeAllProjects();
        $projectIds = $this->accessibleProjectIds($canSeeAll);

        $loginsQuery = ProjectLogin::query()
            ->with('project:id,name,color')
            ->orderBy('name');

        if (! $canSeeAll) {
            $loginsQuery->whereIn('project_id', $projectIds)
                ->whereHas('project', fn ($query) => $query->where('status_id', 3));
        }

        if (! empty($filters['project_id'])) {
            $loginsQuery->where('project_id', $filters['project_id']);
        }

        if (! empty($filters['q'])) {
            $term = $filters['q'];
            $loginsQuery->where(function ($query) use ($term): void {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('user', 'like', "%{$term}%")
                    ->orWhere('url', 'like', "%{$term}%")
                    ->orWhereHas('project', fn ($projectQuery) => $projectQuery->where('name', 'like', "%{$term}%"));
            });
        }

        $logins = $loginsQuery->paginate(20)->withQueryString();

        $projectsQuery = Project::query()->orderBy('name')->select('id', 'name');
        if (! $canSeeAll) {
            $projectsQuery->whereIn('id', $projectIds);
        }
        $projects = $projectsQuery->get();

        return view('project_logins.index', [
            'logins' => $logins,
            'projects' => $projects,
            'filters' => $filters,
            'canSeeAll' => $canSeeAll,
        ]);
    }

    public function create(Project $project): View
    {
        $this->authorizeAccess($project->id);

        return view('project_logins.create', [
            'project' => $project,
            'login' => new ProjectLogin,
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeAccess($project->id);

        $data = $this->validateData($request);
        $data['project_id'] = $project->id;

        ProjectLogin::create($data);

        return redirect()->route('projects.show', $project)->with('status', 'Login creado.');
    }

    public function quickStore(Request $request): RedirectResponse
    {
        $projectId = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
        ])['project_id'];

        $this->authorizeAccess((int) $projectId);

        $data = $this->validateData($request);
        $data['project_id'] = $projectId;

        ProjectLogin::create($data);

        return redirect()->route('logins.index')->with('status', 'Login creado.');
    }

    public function edit(Project $project, ProjectLogin $login): View
    {
        $this->authorizeAccess($project->id);
        $this->ensureSameProject($project, $login);

        return view('project_logins.edit', [
            'project' => $project,
            'login' => $login,
        ]);
    }

    public function update(Request $request, Project $project, ProjectLogin $login): RedirectResponse
    {
        $this->authorizeAccess($project->id);
        $this->ensureSameProject($project, $login);

        $data = $this->validateData($request);
        $login->update($data);

        return redirect()->route('projects.show', $project)->with('status', 'Login actualizado.');
    }

    public function destroy(Project $project, ProjectLogin $login): RedirectResponse
    {
        $this->authorizeAccess($project->id);
        $this->ensureSameProject($project, $login);

        $login->delete();

        return redirect()->route('projects.show', $project)->with('status', 'Login eliminado.');
    }

    protected function authorizeAccess(int $projectId): void
    {
        if ($this->canSeeAllProjects()) {
            return;
        }

        $hasAccess = DB::table('project_users')
            ->where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->exists();

        abort_unless($hasAccess, 403, 'No autorizado para gestionar logins de este proyecto.');
    }

    protected function ensureSameProject(Project $project, ProjectLogin $login): void
    {
        abort_unless($login->project_id === $project->id, 404);
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:250'],
            'user' => ['required', 'string', 'max:250'],
            'password' => ['required', 'string', 'max:250'],
            'url' => ['nullable', 'string'],
        ]);
    }

    protected function canSeeAllProjects(): bool
    {
        $roleId = Auth::user()?->role_id;
        if (! $roleId) {
            return false;
        }

        $moduleId = DB::table('modules')->where('slug', 'logins')->value('id');
        if (! $moduleId) {
            return false;
        }

        $scope = DB::table('role_modules')
            ->where('role_id', $roleId)
            ->where('module_id', $moduleId)
            ->value('view_scope');

        return (int) ($scope ?? 0) === 1;
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    protected function accessibleProjectIds(bool $canSeeAll)
    {
        if ($canSeeAll) {
            return Project::query()->pluck('id');
        }

        return DB::table('project_users')
            ->join('projects', 'projects.id', '=', 'project_users.project_id')
            ->where('user_id', Auth::id())
            ->where('projects.status_id', 3)
            ->pluck('project_id');
    }
}
