<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private readonly AuditService $auditService) {}

    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'department'   => 'nullable|string|max:100',
            'current_password'      => 'nullable|string',
            'password'              => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // Password change
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->fill([
            'full_name'    => $validated['full_name'],
            'phone_number' => $validated['phone_number'],
            'department'   => $validated['department'],
        ])->save();

        $this->auditService->log('update_profile', 'User', $user->id, null, null, 'Updated own profile');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['password' => 'required']);

        $user = auth()->user();
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
