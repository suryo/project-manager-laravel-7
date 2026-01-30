<?php

namespace App\Http\Controllers;

use App\Models\TicketApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Approve a ticket approval
     */
    public function approve(Request $request, $id)
    {
        $approval = TicketApproval::findOrFail($id);
        
        // Verify user has permission (email matches)
        if ($approval->approver_email !== Auth::user()->email) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to approve this ticket.'
            ], 403);
        }
        
        // Check if already processed
        if ($approval->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This approval has already been processed.'
            ], 400);
        }
        
        // Update approval
        $approval->update([
            'status' => 'approved',
            'approver_id' => Auth::id(),
            'approved_at' => now(),
            'ip_address' => $request->ip(),
            'comment' => $request->input('comment')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ticket approved successfully!'
        ]);
    }

    /**
     * Reject a ticket approval
     */
    public function reject(Request $request, $id)
    {
        $approval = TicketApproval::findOrFail($id);
        
        // Verify user has permission (email matches)
        if ($approval->approver_email !== Auth::user()->email) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this ticket.'
            ], 403);
        }
        
        // Check if already processed
        if ($approval->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This approval has already been processed.'
            ], 400);
        }
        
        // Update approval
        $approval->update([
            'status' => 'rejected',
            'approver_id' => Auth::id(),
            'approved_at' => now(),
            'ip_address' => $request->ip(),
            'comment' => $request->input('comment')
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ticket rejected successfully!'
        ]);
    }
}
