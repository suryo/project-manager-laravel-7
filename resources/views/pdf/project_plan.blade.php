<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Plan & Jadwal - {{ $ticket ? $ticket->ticket_number : 'Template' }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 10pt; 
            line-height: 1.4; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 3px solid #000; 
            padding-bottom: 10px; 
        }
        .header h1 { margin: 0; font-size: 18pt; }
        .section { margin: 20px 0; }
        .section h2 { 
            background: #333; 
            color: white; 
            padding: 8px; 
            font-size: 13pt; 
            margin-top: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0; 
            font-size: 9pt;
        }
        table th, table td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: left;
        }
        table th { 
            background: #e0e0e0; 
            font-weight: bold;
        }
        .status-pending { color: #856404; }
        .status-in-progress { color: #004085; }
        .status-completed { color: #155724; }
        .status-todo { color: #856404; }
        .status-done { color: #155724; }
        .summary-box {
            background: #f8f9fa;
            border: 2px solid #333;
            padding: 10px;
            margin: 15px 0;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            font-size: 12pt;
        }
        .stats {
            display: table;
            width: 100%;
        }
        .stat-item {
            display: table-cell;
            width: 33%;
            text-align: center;
            padding: 5px;
        }
        .stat-value {
            font-size: 16pt;
            font-weight: bold;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROJECT PLAN & JADWAL</h1>
        @if($ticket && $ticket->project)
        <p><strong>Project:</strong> {{ $ticket->project->title }}</p>
        @endif
        @if($ticket)
        <p><strong>Ticket:</strong> {{ $ticket->ticket_number }} - {{ $ticket->title }}</p>
        @endif
    </div>

    <table>
        <tr>
            <th width="25%">Nomor Dokumen</th>
            <td>PP-{{ $ticket ? $ticket->ticket_number : '........' }}</td>
        </tr>
        <tr>
            <th>Tanggal Dokumen</th>
            <td>{{ now()->format('d F Y') }}</td>
        </tr>
        @if($ticket && $ticket->project)
        <tr>
            <th>Project Manager</th>
            <td>{{ $ticket->project->user ? $ticket->project->user->name : '-' }}</td>
        </tr>
        <tr>
            <th>Periode</th>
            <td>{{ $ticket->project->start_date ? $ticket->project->start_date->format('d M Y') : '-' }} s/d {{ $ticket->project->end_date ? $ticket->project->end_date->format('d M Y') : '-' }}</td>
        </tr>
        @endif
    </table>

    @if(isset($tasks) && $tasks->count() > 0)
    <div class="summary-box">
        <h3>Project Summary</h3>
        <div class="stats">
            <div class="stat-item">
                <span class="stat-value">{{ $tasks->count() }}</span>
                <small>Total Tasks</small>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $tasks->where('status', 'completed')->count() }}</span>
                <small>Completed</small>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ round(($tasks->where('status', 'completed')->count() / $tasks->count()) * 100) }}%</span>
                <small>Progress</small>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Task Schedule</h2>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Task</th>
                    <th width="20%">Assigned To</th>
                    <th width="15%">Status</th>
                    <th width="10%">Start Date</th>
                    <th width="10%">Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $task->title }}</td>
                    <td>
                        @if($task->assignees->count() > 0)
                            {{ $task->assignees->pluck('name')->join(', ') }}
                        @else
                            Unassigned
                        @endif
                    </td>
                    <td class="status-{{ $task->status }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</td>
                    <td>{{ $task->start_date ? $task->start_date->format('d M Y') : '-' }}</td>
                    <td>{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <p><em>No tasks found for this project.</em></p>
    </div>
    @endif

    <p style="margin-top: 40px; text-align: center; color: #666; font-size: 8pt;">
        Generated on {{ $generatedAt }} | Ticketing System - Project Plan & Schedule
    </p>
</body>
</html>
