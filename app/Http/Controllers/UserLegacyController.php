<?php

namespace App\Http\Controllers;

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

    public function index()
    {
        $users = DB::table('users')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('user_statuses', 'user_statuses.id', '=', 'users.status_id')
            ->select('users.*', 'roles.name as role_name', 'user_statuses.name as status_name')
            ->orderByDesc('users.status_id')
            ->orderBy('users.name')
            ->get();

        return view('users.index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => DB::table('roles')->orderBy('name')->get(),
            'statuses' => DB::table('user_statuses')->orderBy('name')->get(),
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
        ]);

        $payload = $this->mapUserPayload($data);

        if ($request->hasFile('image_url')) {
            $payload['image_url'] = $request->file('image_url')->store('files/users', 'public');
        }

        User::create($payload);

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

        return view('users.show', ['user' => $user]);
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', [
            'user' => $user,
            'roles' => DB::table('roles')->orderBy('name')->get(),
            'statuses' => DB::table('user_statuses')->orderBy('name')->get(),
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
        ]);

        $payload = $this->mapUserPayload($data);

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->input('password'));
        } else {
            unset($payload['password']);
        }

        if ($request->hasFile('image_url')) {
            if ($user->image_url) {
                Storage::disk('public')->delete($user->image_url);
            }
            $payload['image_url'] = $request->file('image_url')->store('files/users', 'public');
        }

        $user->update($payload);

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
            'color' => $data['color'] ?? null,
            'availability' => $data['availability'] ?? null,
            'enterprise_id' => $data['enterprise_id'] ?? null,
            'facebook_id' => $data['facebook_id'] ?? null,
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
