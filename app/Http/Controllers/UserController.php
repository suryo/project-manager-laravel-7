<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('departments')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = \App\Models\Department::orderBy('name')->get();
        return view('users.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'user', 'client'])],
            'departments' => ['nullable', 'array'],
            'departments.*' => ['exists:departments,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['departments'])) {
            $user->departments()->attach($validated['departments'], ['joined_at' => now()]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments = \App\Models\Department::orderBy('name')->get();
        $userDepartmentIds = $user->departments()->pluck('departments.id')->toArray();
        return view('users.edit', compact('user', 'departments', 'userDepartmentIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'user', 'client'])],
            'departments' => ['nullable', 'array'],
            'departments.*' => ['exists:departments,id'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Sync departments
        if (isset($validated['departments'])) {
            $syncData = [];
            foreach ($validated['departments'] as $deptId) {
                $syncData[$deptId] = ['joined_at' => now()];
            }
            $user->departments()->sync($syncData);
        } else {
            $user->departments()->detach();
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
            ->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Login as another user (impersonation)
     */
    public function loginAs(User $user)
    {
        // Prevent self-impersonation
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot impersonate yourself.');
        }

        // Store original user ID in session
        session(['impersonate_original_user' => auth()->id()]);

        // Login as the target user
        auth()->login($user);

        return redirect()->route('home')
            ->with('success', "You are now logged in as {$user->name}. Click 'Leave Impersonation' to return.");
    }

    /**
     * Leave impersonation and return to original user
     */
    public function leaveImpersonation()
    {
        // Get original user ID
        $originalUserId = session('impersonate_original_user');

        if (!$originalUserId) {
            return redirect()->route('home')
                ->with('error', 'No impersonation session found.');
        }

        // Find original user
        $originalUser = User::find($originalUserId);

        if (!$originalUser) {
            return redirect()->route('home')
                ->with('error', 'Original user not found.');
        }

        // Clear impersonation session
        session()->forget('impersonate_original_user');

        // Login back as original user
        auth()->login($originalUser);

        return redirect()->route('users.index')
            ->with('success', 'You have returned to your account.');
    }
}
