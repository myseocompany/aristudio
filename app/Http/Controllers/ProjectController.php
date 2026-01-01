<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorizeModule($request, '/projects', [
                'index' => 'list',
                'create' => 'create',
                'store' => 'create',
                'show' => 'read',
                'edit' => 'update',
                'update' => 'update',
                'destroy' => 'delete',
            ]);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $statuses = DB::table('project_statuses')->orderBy('id')->get();
        $types = DB::table('project_types')->orderBy('name')->get();
        $users = User::orderBy('name')->get(['id', 'name', 'status_id', 'image_url']);

        // Por defecto mostrar "running" (status_id = 3) salvo que se envíe ?status=
        $statusFilter = $request->has('status') ? $request->input('status') : 3;
        $search = $request->input('q');

        $projects = Project::query()
            ->leftJoin('project_statuses', 'project_statuses.id', '=', 'projects.status_id')
            ->leftJoin('project_types', 'project_types.id', '=', 'projects.type_id')
            ->select('projects.*', 'project_statuses.name as status_name', 'project_types.name as type_name')
            ->when($statusFilter !== null && $statusFilter !== '', fn ($q) => $q->where('projects.status_id', $statusFilter))
            ->when($search, fn ($q) => $q->where('projects.name', 'like', '%'.$search.'%'))
            ->withCount('users')
            ->orderBy('projects.weight')
            ->orderBy('projects.name')
            ->paginate(12)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'statuses' => $statuses,
            'types' => $types,
            'statusFilter' => $statusFilter,
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function create()
    {
        $statuses = DB::table('project_statuses')->orderBy('name')->get();
        $types = DB::table('project_types')->orderBy('name')->get();
        $users = User::where('status_id', 1)->orderBy('name')->get(['id', 'name', 'status_id', 'image_url']);

        return view('projects.create', [
            'project' => new Project,
            'statuses' => $statuses,
            'types' => $types,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $userIds = $request->input('user_ids', []);

        $project = Project::create($data);
        $project->users()->sync($userIds);

        return redirect()->route('projects.index')->with('status', 'Proyecto creado.');
    }

    public function show(Project $project)
    {
        $status = DB::table('project_statuses')->where('id', $project->status_id)->value('name');
        $type = DB::table('project_types')->where('id', $project->type_id)->value('name');
        $project->load(['users' => function ($query) {
            $query->select('users.id', 'users.name', 'users.image_url', 'users.status_id', 'users.role_id');
        }]);
        $roleNames = DB::table('roles')->pluck('name', 'id');
        $statusNames = DB::table('user_statuses')->pluck('name', 'id');
        $canManageLogins = DB::table('project_users')
            ->where('project_id', $project->id)
            ->where('user_id', Auth::id())
            ->exists();
        $logins = $canManageLogins
            ? DB::table('project_logins')->where('project_id', $project->id)->orderBy('name')->get()
            : collect();

        return view('projects.show', [
            'project' => $project,
            'statusName' => $status,
            'typeName' => $type,
            'roleNames' => $roleNames,
            'userStatusNames' => $statusNames,
            'logins' => $logins,
            'canManageLogins' => $canManageLogins,
        ]);
    }

    public function edit(Project $project)
    {
        $statuses = DB::table('project_statuses')->orderBy('name')->get();
        $types = DB::table('project_types')->orderBy('name')->get();
        $selectedUsers = $project->users()->pluck('users.id')->toArray();
        $users = User::where('status_id', 1)
            ->orWhereIn('id', $selectedUsers)
            ->orderByDesc(DB::raw('status_id = 1'))
            ->orderBy('name')
            ->get(['id', 'name', 'status_id', 'image_url']);

        return view('projects.edit', [
            'project' => $project,
            'statuses' => $statuses,
            'types' => $types,
            'users' => $users,
            'selectedUsers' => $selectedUsers,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validateData($request, $project->id);
        $userIds = $request->input('user_ids', []);
        $project->update($data);
        $project->users()->sync($userIds);

        return redirect()->route('projects.show', $project)->with('status', 'Proyecto actualizado.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Proyecto eliminado.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
            'weight' => ['nullable', 'numeric'],
            'budget' => ['nullable', 'numeric'],
            'ads_budget' => ['nullable', 'numeric'],
            'start_date' => ['nullable', 'date'],
            'finish_date' => ['nullable', 'date'],
            'weekly_pieces' => ['nullable', 'integer'],
            'status_id' => ['nullable', 'integer'],
            'lead_target' => ['nullable', 'integer'],
            'monthly_points_goal' => ['nullable', 'integer'],
            'sales' => ['nullable', 'numeric'],
            'color' => ['nullable', 'string', 'max:20'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);
    }
}
