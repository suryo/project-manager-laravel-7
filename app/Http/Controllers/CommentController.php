<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->content = $validated['content'];
        $comment->task_id = $task->id;
        $comment->user_id = Auth::id();
        $comment->save();

        if ($request->ajax()) {
            $task->load('comments.user', 'assignees', 'project');
            return response()->json([
                'success' => true,
                'modal_html' => view('tasks.partials.modal_content', compact('task'))->render()
            ]);
        }

        return redirect()->back()->with('success', 'Comment added successfully.');
    }
}
