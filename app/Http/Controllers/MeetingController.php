<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Models\Department;
use App\Models\MeetingAttendance;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::with(['department', 'creator', 'attendances']);
        
        // Filter by department if provided
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        $meetings = $query->latest('meeting_date')->paginate(15);
        $departments = Department::orderBy('name')->get();
        
        return view('meetings.index', compact('meetings', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('meetings.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|max:255',
            'description' => 'nullable',
            'meeting_date' => 'required|date',
            'location' => 'nullable|max:255',
            'notes' => 'nullable',
            'attendees' => 'nullable|array',
            'attendees.*' => 'exists:users,id',
        ]);

        $meeting = Meeting::create([
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_date' => $request->meeting_date,
            'location' => $request->location,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Add attendees if provided
        if ($request->has('attendees') && is_array($request->attendees)) {
            foreach ($request->attendees as $userId) {
                MeetingAttendance::create([
                    'meeting_id' => $meeting->id,
                    'user_id' => $userId,
                    'status' => 'present',
                ]);
            }
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting created successfully.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['department', 'creator', 'attendances.user']);
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $departments = Department::orderBy('name')->get();
        $meeting->load('attendances.user');
        return view('meetings.edit', compact('meeting', 'departments'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'title' => 'required|max:255',
            'description' => 'nullable',
            'meeting_date' => 'required|date',
            'location' => 'nullable|max:255',
            'notes' => 'nullable',
        ]);

        $meeting->update($request->only([
            'department_id',
            'title',
            'description',
            'meeting_date',
            'location',
            'notes',
        ]));

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Meeting updated successfully.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')
            ->with('success', 'Meeting deleted successfully.');
    }

    public function markAttendance(Request $request, Meeting $meeting)
    {
        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,excused',
            'attendances.*.notes' => 'nullable',
        ]);

        foreach ($request->attendances as $attendance) {
            MeetingAttendance::updateOrCreate(
                [
                    'meeting_id' => $meeting->id,
                    'user_id' => $attendance['user_id'],
                ],
                [
                    'status' => $attendance['status'],
                    'notes' => $attendance['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('meetings.show', $meeting)
            ->with('success', 'Attendance updated successfully.');
    }
}
