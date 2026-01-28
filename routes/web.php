<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------------------------------|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (!session()->has('locale')) {
        session(['locale' => 'id']);
    }
    App::setLocale(session('locale'));
    return view('welcome');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/projects/{project}/toggle-pin', [App\Http\Controllers\ProjectController::class, 'togglePin'])->name('projects.toggle-pin');
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    Route::post('/projects/{project}/mgmt-update', [App\Http\Controllers\ProjectController::class, 'updateMgmt'])->name('projects.mgmt-update');
    Route::post('/projects/{project}/tasks/mgmt-update', [App\Http\Controllers\ProjectController::class, 'updateTaskMgmt'])->name('projects.task-mgmt-update');
    Route::post('/projects/{project}/tickets/mgmt-update', [App\Http\Controllers\ProjectController::class, 'updateTicketMgmt'])->name('projects.ticket-mgmt-update');
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
    Route::get('/tasks/{task}/details', [App\Http\Controllers\TaskController::class, 'details'])->name('tasks.details');
    Route::get('/tasks/{task}/poac-logs', [App\Http\Controllers\TaskController::class, 'getPoacLogs'])->name('tasks.poac-logs');
    Route::post('/tasks/{task}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('tasks.comments.store');

    // Ticketing System Routes
    Route::resource('tickets', App\Http\Controllers\TicketController::class);
    Route::post('tickets/{ticket}/assign', [App\Http\Controllers\TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{ticket}/stage', [App\Http\Controllers\TicketController::class, 'progressStage'])->name('tickets.stage');
    Route::post('tickets/{ticket}/complete', [App\Http\Controllers\TicketController::class, 'complete'])->name('tickets.complete');
    Route::post('tickets/{ticket}/update-status', [App\Http\Controllers\TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('tickets/{ticket}/link-project', [App\Http\Controllers\TicketController::class, 'linkProject'])->name('tickets.link-project');
    Route::get('/tickets/{ticket}/poac-logs', [App\Http\Controllers\TicketController::class, 'getPoacLogs'])->name('tickets.poac-logs');
    
    // Ticket Document Routes
    Route::post('tickets/{ticket}/documents', [App\Http\Controllers\TicketDocumentController::class, 'upload'])->name('tickets.documents.upload');
    Route::get('tickets/documents/{document}/download', [App\Http\Controllers\TicketDocumentController::class, 'download'])->name('tickets.documents.download');
    Route::post('tickets/documents/{document}/approve', [App\Http\Controllers\TicketDocumentController::class, 'approve'])->name('tickets.documents.approve');
    Route::post('tickets/documents/{document}/reject', [App\Http\Controllers\TicketDocumentController::class, 'reject'])->name('tickets.documents.reject');
    Route::delete('tickets/documents/{document}', [App\Http\Controllers\TicketDocumentController::class, 'destroy'])->name('tickets.documents.destroy');
    
    // Enhanced Document Routes
    Route::get('tickets/{ticket}/documents/form/{type}', [App\Http\Controllers\TicketDocumentController::class, 'createForm'])->name('tickets.documents.form');
    Route::post('tickets/{ticket}/documents/form', [App\Http\Controllers\TicketDocumentController::class, 'storeForm'])->name('tickets.documents.store-form');
    Route::post('tickets/{ticket}/documents/multiple', [App\Http\Controllers\TicketDocumentController::class, 'uploadMultiple'])->name('tickets.documents.upload-multiple');
    Route::get('documents/template/{type}', [App\Http\Controllers\TicketDocumentController::class, 'downloadTemplate'])->name('tickets.documents.template');
    Route::post('tickets/{ticket}/documents/generate-plan', [App\Http\Controllers\TicketDocumentController::class, 'generateProjectPlan'])->name('tickets.documents.generate-plan');

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::resource('departments', App\Http\Controllers\DepartmentController::class);
        Route::resource('statuses', App\Http\Controllers\ProjectStatusController::class)->except(['show']);
        
        // Impersonation
        Route::post('/users/{user}/login-as', [App\Http\Controllers\UserController::class, 'loginAs'])->name('users.login-as');
    });
    
    // Leave impersonation (accessible by impersonated user)
    Route::post('/leave-impersonation', [App\Http\Controllers\UserController::class, 'leaveImpersonation'])->name('leave-impersonation');
    
    // Department Notes
    Route::get('/notes', [App\Http\Controllers\DepartmentNoteController::class, 'allNotes'])->name('notes.all');
    Route::resource('departments.notes', App\Http\Controllers\DepartmentNoteController::class)->except(['show', 'create', 'edit']);
    Route::post('departments/{department}/notes/{note}/toggle-pin', [App\Http\Controllers\DepartmentNoteController::class, 'togglePin'])->name('departments.notes.toggle-pin');
    
    // POAC Logs (Admin only)
    Route::get('/poac-logs', [App\Http\Controllers\PoacLogController::class, 'index'])->name('poac-logs.index');
    Route::get('/poac-logs/report', [App\Http\Controllers\PoacLogController::class, 'showReportForm'])->name('poac-logs.report');
    Route::post('/poac-logs/generate-report', [App\Http\Controllers\PoacLogController::class, 'generateReport'])->name('poac-logs.generate-report');
});

// Public Ticket Request (no auth required)
Route::name('public.')->group(function () {
    Route::get('ticket-request', [App\Http\Controllers\PublicTicketRequestController::class, 'showForm'])
        ->name('ticket-request');
    Route::post('ticket-request', [App\Http\Controllers\PublicTicketRequestController::class, 'submitRequest'])
        ->name('ticket-request.submit');
    Route::get('ticket-request/view/{token}', [App\Http\Controllers\PublicTicketRequestController::class, 'viewRequest'])
        ->name('ticket-request.view');

    Route::get('ticket-request/success/{token}', [App\Http\Controllers\PublicTicketRequestController::class, 'showSuccess'])
        ->name('ticket-request.success');
    Route::get('ticket-request/status/{token}', [App\Http\Controllers\PublicTicketRequestController::class, 'checkStatus'])
        ->name('ticket-request.status');
    Route::post('ticket-request/status/{token}', [App\Http\Controllers\PublicTicketRequestController::class, 'updateStatus'])
        ->name('ticket-request.update-status');
        
    // Approval Routes
    Route::get('approval/{token}', [App\Http\Controllers\PublicTicketRequestController::class, 'showApprovalPage'])
        ->name('approval.show');
    Route::post('approval/{token}', [App\Http\Controllers\PublicTicketRequestController::class, 'submitApproval'])
        ->name('approval.submit');
});

// Department Members Management
Route::middleware(['auth'])->group(function () {
    Route::get('departments/{department}/members', [App\Http\Controllers\DepartmentController::class, 'members'])->name('departments.members');
    Route::post('departments/{department}/members', [App\Http\Controllers\DepartmentController::class, 'addMember'])->name('departments.members.add');
    Route::delete('departments/{department}/members/{userId}', [App\Http\Controllers\DepartmentController::class, 'removeMember'])->name('departments.members.remove');
    
    // Meetings
    Route::resource('meetings', App\Http\Controllers\MeetingController::class);
    Route::post('meetings/{meeting}/attendance', [App\Http\Controllers\MeetingController::class, 'markAttendance'])->name('meetings.attendance');
});

// Department Landing Page (Public)
Route::get('/department/{slug}', [App\Http\Controllers\DepartmentController::class, 'landingPage'])->name('department.landing');
Route::get('/department/{slug}/meeting/create', [App\Http\Controllers\DepartmentController::class, 'createMeetingPublic'])->name('department.meeting.create');
Route::get('/department/{slug}/meeting/{meetingId}', [App\Http\Controllers\DepartmentController::class, 'showMeetingPublic'])->name('department.meeting.show');
Route::get('/department/{slug}/projects/{projectSlug}', [App\Http\Controllers\DepartmentController::class, 'showProjectPublic'])->name('department.projects.show');

// Department Chat Routes
Route::get('/department/{slug}/chat/messages', [App\Http\Controllers\DepartmentChatController::class, 'fetchMessages'])->name('department.chat.fetch');
Route::post('/department/{slug}/chat/send', [App\Http\Controllers\DepartmentChatController::class, 'sendMessage'])->name('department.chat.send');

// Department Project Management Update
Route::post('/department/{slug}/projects/{projectSlug}/mgmt-update', [App\Http\Controllers\DepartmentController::class, 'updateMgmtPhase'])->name('department.project.mgmt-update');
Route::post('/department/{slug}/projects/{projectSlug}/tasks/mgmt-update', [App\Http\Controllers\DepartmentController::class, 'updateTaskMgmt'])->name('department.task.mgmt-update');
