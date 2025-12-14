<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $modules = Module::orderBy('weight')->orderBy('name')->get();

        return view('modules.index', compact('modules'));
    }

    public function create()
    {
        return view('modules.create', ['module' => new Module]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Module::create($data);

        return redirect()->route('modules.index')->with('status', 'Módulo creado.');
    }

    public function show(Module $module)
    {
        return view('modules.show', compact('module'));
    }

    public function edit(Module $module)
    {
        return view('modules.edit', compact('module'));
    }

    public function update(Request $request, Module $module)
    {
        $data = $this->validateData($request, $module->id);
        $module->update($data);

        return redirect()->route('modules.index')->with('status', 'Módulo actualizado.');
    }

    public function destroy(Module $module)
    {
        DB::table('role_modules')->where('module_id', $module->id)->delete();
        $module->delete();

        return redirect()->route('modules.index')->with('status', 'Módulo eliminado.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        $order = $request->input('order');
        DB::transaction(function () use ($order) {
            foreach ($order as $index => $moduleId) {
                Module::where('id', $moduleId)->update(['weight' => $index]);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    private function validateData(Request $request, ?int $moduleId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('modules', 'name')->ignore($moduleId)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('modules', 'slug')->ignore($moduleId)],
            'weight' => ['nullable', 'integer'],
        ]);
    }
}
