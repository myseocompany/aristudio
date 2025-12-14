<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Project $project)
    {
        $this->authorizeAccess($project->id);

        return view('project_logins.create', [
            'project' => $project,
            'login' => new ProjectLogin,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeAccess($project->id);

        $data = $this->validateData($request);
        $data['project_id'] = $project->id;

        ProjectLogin::create($data);

        return redirect()->route('projects.show', $project)->with('status', 'Login creado.');
    }

    public function edit(Project $project, ProjectLogin $login)
    {
        $this->authorizeAccess($project->id);
        $this->ensureSameProject($project, $login);

        return view('project_logins.edit', [
            'project' => $project,
            'login' => $login,
        ]);
    }

    public function update(Request $request, Project $project, ProjectLogin $login)
    {
        $this->authorizeAccess($project->id);
        $this->ensureSameProject($project, $login);

        $data = $this->validateData($request);
        $login->update($data);

        return redirect()->route('projects.show', $project)->with('status', 'Login actualizado.');
    }

    public function destroy(Project $project, ProjectLogin $login)
    {
        $this->authorizeAccess($project->id);
        $this->ensureSameProject($project, $login);

        $login->delete();

        return redirect()->route('projects.show', $project)->with('status', 'Login eliminado.');
    }

    protected function authorizeAccess(int $projectId): void
    {
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
}
