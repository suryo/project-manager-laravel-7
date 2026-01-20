<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DepartmentMessage;
use Auth;

class DepartmentChatController extends Controller
{
    public function fetchMessages($slug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        
        // Fetch messages (limit 50 recent)
        $messages = DepartmentMessage::where('department_id', $department->id)
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        // Get online members (only those who are members of this department)
        $onlineMembers = $department->members()
            ->where('users.last_seen_at', '>', now()->subMinutes(5))
            ->get(['users.id', 'users.name', 'users.last_seen_at']);

        return response()->json([
            'messages' => $messages,
            'online_members' => $onlineMembers
        ]);
    }

    public function sendMessage(Request $request, $slug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();

        $rules = [
            'message' => 'required|string|max:1000',
        ];

        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
            $rules['guest_contact'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        $message = new DepartmentMessage();
        $message->department_id = $department->id;
        $message->message = $data['message'];

        if (Auth::check()) {
            $message->user_id = Auth::id();
        } else {
            // Guest logic
            $message->guest_name = $data['guest_name'];
            $message->guest_email = $data['guest_email'];
            $message->guest_contact = $data['guest_contact'];
        }

        $message->save();

        return response()->json([
            'success' => true,
            'message' => $message->load('user')
        ]);
    }
}
