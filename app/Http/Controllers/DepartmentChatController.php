<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\DepartmentMessage;
use Auth;

class DepartmentChatController extends Controller
{
    public function fetchMessages(Request $request, $slug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();
        $conversationId = $request->query('conversation_id');
        $isStaff = Auth::check() && $department->members()->where('user_id', Auth::id())->exists();

        // If staff and no conversation_id, return list of active conversations (Inbox)
        if ($isStaff && !$conversationId) {
            $conversations = DepartmentMessage::where('department_id', $department->id)
                ->select('conversation_id', 'guest_name', 'guest_email', 'user_id', 'message', 'created_at', 'is_from_staff', 'read_at')
                ->whereIn('id', function($query) use ($department) {
                    $query->selectRaw('max(id)')
                        ->from('department_messages')
                        ->where('department_id', $department->id)
                        ->groupBy('conversation_id');
                })
                ->with('user')
                ->latest()
                ->get();

            // Calculate unread count for each conversation (messages from client that are not read)
            foreach($conversations as $conv) {
                $conv->unread_count = DepartmentMessage::where('conversation_id', $conv->conversation_id)
                    ->where('is_from_staff', false)
                    ->whereNull('read_at')
                    ->count();
            }

            return response()->json([
                'is_staff' => true,
                'conversations' => $conversations,
                'online_members' => $this->getOnlineMembers($department)
            ]);
        }

        // Fetch messages for a specific conversation
        $query = DepartmentMessage::where('department_id', $department->id);
        
        if ($conversationId) {
            $query->where('conversation_id', $conversationId);
            
            // MARK AS READ: 
            // If staff is viewing, mark all client messages in this conversation as read
            // If client is viewing, mark all staff messages in this conversation as read
            DepartmentMessage::where('conversation_id', $conversationId)
                ->where('is_from_staff', !$isStaff) // if I am staff, mark client messages (!isStaff); if I am client, mark staff messages (isStaff)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        } else {
            $query->whereNull('conversation_id');
        }

        $messages = $query->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        // Calculate total unread for client (on the main button)
        $totalUnread = 0;
        if (!$isStaff && $conversationId) {
            $totalUnread = DepartmentMessage::where('conversation_id', $conversationId)
                ->where('is_from_staff', true)
                ->whereNull('read_at')
                ->count();
        }

        return response()->json([
            'is_staff' => $isStaff,
            'messages' => $messages,
            'total_unread' => $totalUnread,
            'online_members' => $this->getOnlineMembers($department)
        ]);
    }

    protected function getOnlineMembers($department)
    {
        return $department->members()
            ->where('users.last_seen_at', '>', now()->subMinutes(5))
            ->get(['users.id', 'users.name', 'users.last_seen_at']);
    }

    public function sendMessage(Request $request, $slug)
    {
        $department = Department::where('slug', $slug)->firstOrFail();

        $rules = [
            'message' => 'required|string|max:1000',
            'conversation_id' => 'required|string|max:255',
        ];

        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
            $rules['guest_contact'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        $isStaff = Auth::check() && $department->members()->where('user_id', Auth::id())->exists();

        $message = new DepartmentMessage();
        $message->department_id = $department->id;
        $message->conversation_id = $data['conversation_id'];
        $message->message = $data['message'];
        $message->is_from_staff = $isStaff;

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
