<?php

namespace App\Http\Controllers;

use App\Models\PoacLog;
use Illuminate\Http\Request;

class PoacLogController extends Controller
{
    public function index(Request $request)
    {
        // Admin sees all logs, regular users see only their own logs
        $query = PoacLog::with(['user', 'poacable']);
        
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

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
    
    public function showReportForm()
    {
        return view('poac-logs.report');
    }
    
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:custom,today,week,month,year',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2020|max:2100',
        ]);
        
        $query = PoacLog::with(['user', 'poacable']);
        
        // Filter by user (admin sees all, users see their own)
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        
        // Apply date filters based on report type
        switch ($request->report_type) {
            case 'today':
                $query->whereDate('created_at', today());
                $periodLabel = 'Today (' . today()->format('d M Y') . ')';
                break;
                
            case 'week':
                $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
                $periodLabel = 'This Week (' . now()->startOfWeek()->format('d M') . ' - ' . now()->endOfWeek()->format('d M Y') . ')';
                break;
                
            case 'month':
                if ($request->month && $request->year) {
                    $date = \Carbon\Carbon::createFromDate($request->year, $request->month, 1);
                    $query->whereYear('created_at', $request->year)
                          ->whereMonth('created_at', $request->month);
                    $periodLabel = $date->format('F Y');
                } else {
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    $periodLabel = now()->format('F Y');
                }
                break;
                
            case 'year':
                $year = $request->year ?? now()->year;
                $query->whereYear('created_at', $year);
                $periodLabel = 'Year ' . $year;
                break;
                
            case 'custom':
                if ($request->date_from && $request->date_to) {
                    $query->whereBetween('created_at', [
                        $request->date_from . ' 00:00:00',
                        $request->date_to . ' 23:59:59'
                    ]);
                    $periodLabel = \Carbon\Carbon::parse($request->date_from)->format('d M Y') . ' - ' . 
                                   \Carbon\Carbon::parse($request->date_to)->format('d M Y');
                }
                break;
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Generate text report
        $report = "POAC ACTIVITY REPORT\n";
        $report .= "==========================================\n\n";
        $report .= "User: " . auth()->user()->name . "\n";
        $report .= "Period: " . ($periodLabel ?? 'Custom Range') . "\n";
        $report .= "Generated: " . now()->format('d M Y, H:i') . "\n";
        $report .= "Total Logs: " . $logs->count() . "\n\n";
        $report .= "==========================================\n\n";
        
        if ($logs->isEmpty()) {
            $report .= "No activity logs found for this period.\n";
        } else {
            foreach ($logs as $log) {
                $phaseIcons = [
                    'Planning' => '📋',
                    'Organizing' => '🗂️',
                    'Actuating' => '⚡',
                    'Controlling' => '📊'
                ];
                
                $report .= ($phaseIcons[$log->phase] ?? '') . " " . strtoupper($log->phase) . " - " . $log->title . "\n";
                
                if ($log->poacable) {
                    $type = class_basename($log->poacable_type);
                    $name = $log->poacable->title ?? $log->poacable->name ?? 'N/A';
                    $report .= "$type: $name\n";
                }
                
                $report .= "Date: " . $log->created_at->format('d M Y, H:i') . "\n";
                
                // Convert HTML to plain text with proper line breaks
                $description = $log->description;
                // Replace </li> with newline to preserve list items
                $description = str_replace('</li>', "\n", $description);
                // Replace <br> tags with newline
                $description = str_replace(['<br>', '<br/>', '<br />'], "\n", $description);
                // Replace </p> with double newline for paragraph breaks
                $description = str_replace('</p>', "\n\n", $description);
                // Strip remaining HTML tags
                $description = strip_tags($description);
                // Clean up multiple consecutive newlines
                $description = preg_replace("/\n{3,}/", "\n\n", $description);
                // Trim whitespace
                $description = trim($description);
                
                $report .= "Description:\n" . $description . "\n";
                $report .= "\n------------------------------------------\n\n";
            }
        }
        
        return response()->json([
            'success' => true,
            'report' => $report,
            'count' => $logs->count()
        ]);
    }
}
