<?php

namespace App\Http\Controllers;

use App\Models\DepartmentNote;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentNoteController extends Controller
{
    /**
     * Display all notes from all user's departments.
     */
    public function allNotes(Request $request)
    {
        $user = Auth::user();
        
        // Get all department IDs user has access to
        $departmentIds = $user->role === 'admin' 
            ? Department::pluck('id')->toArray()
            : $user->departments->pluck('id')->toArray();
        
        $query = DepartmentNote::with(['user', 'department'])
            ->whereIn('department_id', $departmentIds);
        
        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // Color filter
        if ($request->has('color') && $request->color) {
            $query->byColor($request->color);
        }
        
        // Department filter
        if ($request->has('department') && $request->department) {
            $query->where('department_id', $request->department);
        }
        
        $notes = $query->get();
        $departments = Department::whereIn('id', $departmentIds)->get();
        
        return view('departments.notes.all', compact('notes', 'departments'));
    }
    
    /**
     * Display a listing of notes for a department.
     */
    public function index(Department $department, Request $request)
    {
        // Check if user has access to this department
        $user = Auth::user();
        if ($user->role !== 'admin' && !$user->departments->contains($department->id)) {
            abort(403, 'You do not have access to this department.');
        }

        $query = $department->notes()->with('user');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Color filter
        if ($request->has('color') && $request->color) {
            $query->byColor($request->color);
        }

        $notes = $query->get();

        return view('departments.notes.index', compact('department', 'notes'));
    }

    /**
     * Store a newly created note.
     */
    public function store(Request $request, Department $department)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'required|in:yellow,blue,green,pink,purple,orange',
            'is_pinned' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['department_id'] = $department->id;

        $note = DepartmentNote::create($validated);

        return redirect()->route('departments.notes.index', $department)
            ->with('success', 'Note created successfully!');
    }

    /**
     * Update the specified note.
     */
    public function update(Request $request, Department $department, DepartmentNote $note)
    {
        // Verify note belongs to this department
        if ($note->department_id !== $department->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'required|in:yellow,blue,green,pink,purple,orange',
            'is_pinned' => 'boolean',
        ]);

        $note->update($validated);

        return redirect()->route('departments.notes.index', $department)
            ->with('success', 'Note updated successfully!');
    }

    /**
     * Remove the specified note.
     */
    public function destroy(Department $department, DepartmentNote $note)
    {
        // Verify note belongs to this department
        if ($note->department_id !== $department->id) {
            abort(404);
        }

        $note->delete();

        return redirect()->route('departments.notes.index', $department)
            ->with('success', 'Note deleted successfully!');
    }

    /**
     * Toggle pin status of a note.
     */
    public function togglePin(Department $department, DepartmentNote $note)
    {
        // Verify note belongs to this department
        if ($note->department_id !== $department->id) {
            abort(404);
        }

        $note->update(['is_pinned' => !$note->is_pinned]);

        return back()->with('success', $note->is_pinned ? 'Note pinned!' : 'Note unpinned!');
    }
}
