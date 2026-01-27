<?php

namespace App\Http\Controllers;

use App\Models\PoacLog;
use Illuminate\Http\Request;

class PoacLogController extends Controller
{
    public function index(Request $request)
    {
        // Only admin can access
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $query = PoacLog::with(['user', 'poacable']);

        // Filter by phase
        if ($request->has('phase') && $request->phase) {
            $query->where('phase', $request->phase);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('poacable_type', $request->type);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(20);

        // Get all users for filter
        $users = \App\Models\User::orderBy('name')->get();

        return view('poac-logs.index', compact('logs', 'users'));
    }
}
