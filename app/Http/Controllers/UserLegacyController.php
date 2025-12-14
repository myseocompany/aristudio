<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserLegacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $statuses = DB::table('user_statuses')->orderBy('id')->get();

        // Por defecto mostrar activos (status_id = 1). Si se envía ?status= (vacío), se muestran todos.
        $statusFilter = $request->has('status')
            ? $request->input('status')
            : 1;

        $users = DB::table('users')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('user_statuses', 'user_statuses.id', '=', 'users.status_id')
            ->select('users.*', 'roles.name as role_name', 'user_statuses.name as status_name')
            ->when($statusFilter !== null && $statusFilter !== '', function ($q) use ($statusFilter) {
                return $q->where('users.status_id', $statusFilter);
            })
            ->orderByDesc(DB::raw('users.status_id = 1')) // activos (1) primero
            ->orderBy('users.status_id')
            ->orderBy('users.name')
            ->get();

        return view('users.index', [
            'users' => $users,
            'statuses' => $statuses,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => DB::table('roles')->orderBy('name')->get(),
            'statuses' => DB::table('user_statuses')->orderBy('name')->get(),
            'projects' => Project::where('status_id', 3)
                ->orderBy('name')
                ->get(['id', 'name', 'status_id', 'color']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:250'],
            'position' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:250'],
            'status_id' => ['nullable', 'integer'],
            'role_id' => ['nullable', 'integer'],
            'document' => ['nullable', 'string', 'max:250'],
            'image_url' => ['nullable', 'image'],
            'birth_date' => ['nullable', 'date'],
            'hourly_rate' => ['nullable', 'numeric'],
            'availability' => ['nullable', 'integer'],
            'entry_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'contracted_hours' => ['nullable', 'integer'],
            'contract_type' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'arl' => ['nullable', 'string', 'max:250'],
            'eps' => ['nullable', 'string', 'max:250'],
            'last_login' => ['nullable', 'date'],
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
        ]);

        $payload = $this->mapUserPayload($data);

        if ($request->hasFile('image_url')) {
            $payload['image_url'] = $request->file('image_url')->store('files/users', 'public');
        }

        $user = User::create($payload);

        $projectIds = $request->input('project_ids', []);
        $user->projects()->sync($projectIds);

        return redirect()->route('users.index')->with('status', 'Usuario creado.');
    }

    public function show(int $id)
    {
        $user = DB::table('users')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('user_statuses', 'user_statuses.id', '=', 'users.status_id')
            ->select('users.*', 'roles.name as role_name', 'user_statuses.name as status_name')
            ->where('users.id', $id)
            ->firstOrFail();

        $projects = DB::table('project_users')
            ->join('projects', 'projects.id', '=', 'project_users.project_id')
            ->leftJoin('project_statuses', 'project_statuses.id', '=', 'projects.status_id')
            ->select('projects.id', 'projects.name', 'projects.color', 'projects.status_id', 'project_statuses.name as status_name')
            ->where('project_users.user_id', $id)
            ->orderBy('projects.name')
            ->get();

        return view('users.show', ['user' => $user, 'projects' => $projects]);
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        $selectedProjects = $user->projects()->pluck('projects.id')->toArray();

        return view('users.edit', [
            'user' => $user,
            'roles' => DB::table('roles')->orderBy('name')->get(),
            'statuses' => DB::table('user_statuses')->orderBy('name')->get(),
            'projects' => Project::where('status_id', 3)
                ->orWhereIn('id', $selectedProjects)
                ->orderByDesc(DB::raw('status_id = 3'))
                ->orderBy('name')
                ->get(['id', 'name', 'status_id', 'color']),
            'selectedProjects' => $selectedProjects,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:250'],
            'position' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:250'],
            'status_id' => ['nullable', 'integer'],
            'role_id' => ['nullable', 'integer'],
            'document' => ['nullable', 'string', 'max:250'],
            'image_url' => ['nullable', 'image'],
            'birth_date' => ['nullable', 'date'],
            'hourly_rate' => ['nullable', 'numeric'],
            'availability' => ['nullable', 'integer'],
            'enterprise_id' => ['nullable', 'integer'],
            'facebook_id' => ['nullable', 'integer'],
            'entry_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'contracted_hours' => ['nullable', 'integer'],
            'contract_type' => ['nullable', 'string', 'max:50'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'project_ids' => ['nullable', 'array'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
        ]);

        $payload = $this->mapUserPayload($data);

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->input('password'));
        } else {
            unset($payload['password']);
        }

        // Maneja imagen: solo actualiza si se sube una nueva, de lo contrario conserva la actual.
        if ($request->hasFile('image_url')) {
            if ($user->image_url) {
                Storage::disk('public')->delete($user->image_url);
            }
            $payload['image_url'] = $request->file('image_url')->store('files/users', 'public');
        } else {
            unset($payload['image_url']);
        }

        // No sobrescribir last_login si no viene en el request.
        if (! array_key_exists('last_login', $data)) {
            unset($payload['last_login']);
        }

        $user->update($payload);
        $projectIds = $request->input('project_ids', []);
        $user->projects()->sync($projectIds);

        return redirect()->route('users.index')->with('status', 'Usuario actualizado.');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        if ($user->image_url) {
            Storage::disk('public')->delete($user->image_url);
        }
        $user->delete();

        return redirect()->route('users.index')->with('status', 'Usuario eliminado.');
    }

    protected function mapUserPayload(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) && $data['password'] !== null
                ? Hash::make($data['password'])
                : Hash::make(Str::random(16)),
            'phone' => $data['phone'] ?? null,
            'position' => $data['position'] ?? null,
            'address' => $data['address'] ?? null,
            'status_id' => $data['status_id'] ?? 1,
            'role_id' => $data['role_id'] ?? null,
            'document' => $data['document'] ?? null,
            // Rellenamos con null el resto de campos legacy
            'birth_date' => $data['birth_date'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? null,
            'color' => $data['color'] ?? null,
            'availability' => $data['availability'] ?? null,
            'image_url' => $data['image_url'] ?? null,
            'entry_date' => $data['entry_date'] ?? null,
            'termination_date' => $data['termination_date'] ?? null,
            'contracted_hours' => $data['contracted_hours'] ?? null,
            'contract_type' => $data['contract_type'] ?? null,
            'blood_type' => $data['blood_type'] ?? null,
            'last_login' => $data['last_login'] ?? null,
            'arl' => $data['arl'] ?? null,
            'eps' => $data['eps'] ?? null,
        ];
    }
}
