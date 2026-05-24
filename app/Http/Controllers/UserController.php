<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function index()
    {
        $this->authorize('admin');

        $users = User::query()
            ->with('roles')
            ->withCount(['assignedActivities', 'createdActivities'])
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'employee_id'  => 'required|string|unique:users,employee_id|max:50',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'nullable|string|max:20',
            'department'   => 'nullable|string|max:100',
            'role'         => 'required|exists:roles,name',
            'password'     => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = User::create([
            'full_name'    => $validated['full_name'],
            'employee_id'  => $validated['employee_id'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'department'   => $validated['department'],
            'password'     => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        $this->auditService->log('create_user', 'User', $user->id, null, ['email' => $user->email, 'role' => $validated['role']], "Created user: {$user->full_name}");

        return redirect()->route('admin.users.index')
            ->with('success', "User {$user->full_name} created successfully.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'employee_id'  => 'required|string|max:50|unique:users,employee_id,' . $user->id,
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'department'   => 'nullable|string|max:100',
            'role'         => 'required|exists:roles,name',
            'is_active'    => 'boolean',
        ]);

        $oldRole = $user->primary_role;
        $user->update($validated);
        $user->syncRoles([$validated['role']]);

        $this->auditService->log('update_user', 'User', $user->id, ['role' => $oldRole], ['role' => $validated['role']], "Updated user: {$user->full_name}");

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        $this->auditService->log('toggle_user_status', 'User', $user->id, null, ['is_active' => $user->is_active], "User {$status}: {$user->full_name}");

        return back()->with('success', "User {$status} successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        $this->auditService->log('delete_user', 'User', $user->id, null, null, "Deleted user: {$user->full_name}");

        return redirect()->route('admin.users.index')
            ->with('success', 'User removed.');
    }
}
