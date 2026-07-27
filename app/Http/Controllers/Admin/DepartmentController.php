<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::ordered()->get();
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.departments.index', compact('departments', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
            'code' => 'required|string|max:20|unique:departments,code',
            'director_role' => 'nullable|string|exists:roles,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? Department::max('sort_order') + 1;

        Department::create($validated);

        return back()->with('success', "Departemen \"{$validated['name']}\" berhasil ditambahkan!");
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'director_role' => 'nullable|string|exists:roles,name',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // If name changed, update all users with the old department name
        if ($department->name !== $validated['name']) {
            \App\Models\User::where('department', $department->name)
                ->update(['department' => $validated['name']]);
        }

        $department->update($validated);

        return back()->with('success', "Departemen \"{$validated['name']}\" berhasil diperbarui!");
    }

    public function destroy(Department $department)
    {
        // Check if any users still use this department
        $userCount = \App\Models\User::where('department', $department->name)->count();
        if ($userCount > 0) {
            return back()->with('error', "Tidak bisa menghapus departemen \"{$department->name}\" karena masih digunakan oleh {$userCount} pengguna.");
        }

        $name = $department->name;
        $department->delete();

        return back()->with('success', "Departemen \"{$name}\" berhasil dihapus!");
    }
}
