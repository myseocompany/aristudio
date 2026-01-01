<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorizeModule($request, '/roles', [
                'index' => 'list',
                'create' => 'create',
                'store' => 'create',
                'show' => 'read',
                'edit' => 'update',
                'update' => 'update',
                'destroy' => 'delete',
                'updatePermissions' => 'update',
            ]);

            return $next($request);
        });
    }

    public function index()
    {
        $roles = Role::orderBy('name')->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create', ['role' => new Role]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        Role::create($data);

        return redirect()->route('roles.index')->with('status', 'Rol creado.');
    }

    public function show(Role $role)
    {
        $modules = DB::table('modules')->orderBy('weight')->orderBy('name')->get();
        $permissions = DB::table('role_modules')
            ->where('role_id', $role->id)
            ->get()
            ->mapWithKeys(function ($perm) {
                return [(int) $perm->module_id => $perm];
            });

        return view('roles.show', compact('role', 'modules', 'permissions'));
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
        ]);

        $role->update($data);

        return redirect()->route('roles.index')->with('status', 'Rol actualizado.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        DB::table('role_modules')->where('role_id', $role->id)->delete();

        return redirect()->route('roles.index')->with('status', 'Rol eliminado.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $modules = DB::table('modules')->pluck('id');

        DB::table('role_modules')->where('role_id', $role->id)->delete();

        $now = now();
        foreach ($modules as $moduleId) {
            $perm = $request->input("permissions.$moduleId", []);
            $scopeRaw = $perm['scope'] ?? 0;
            $viewScope = in_array($scopeRaw, [1, '1', 'all'], true) ? 1 : 0;
            $payload = [
                'role_id' => $role->id,
                'module_id' => $moduleId,
                'created' => isset($perm['create']) ? 1 : null,
                'readed' => isset($perm['read']) ? 1 : null,
                'updated' => isset($perm['update']) ? 1 : null,
                'deleted' => isset($perm['delete']) ? 1 : null,
                'list' => isset($perm['list']) ? 1 : null,
                'view_scope' => $viewScope,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Si no hay ningún permiso marcado, no guardamos la fila
            if ($payload['created'] || $payload['readed'] || $payload['updated'] || $payload['deleted'] || $payload['list']) {
                DB::table('role_modules')->insert($payload);
            }
        }

        return redirect()->route('roles.show', $role)->with('status', 'Permisos actualizados.');
    }
}
