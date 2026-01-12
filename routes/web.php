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
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
    Route::get('/tasks/{task}/details', [App\Http\Controllers\TaskController::class, 'details'])->name('tasks.details');
    Route::post('/tasks/{task}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('tasks.comments.store');

    // Ticketing System Routes
    Route::resource('tickets', App\Http\Controllers\TicketController::class);
    Route::post('tickets/{ticket}/assign', [App\Http\Controllers\TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('tickets/{ticket}/stage', [App\Http\Controllers\TicketController::class, 'progressStage'])->name('tickets.stage');
    Route::post('tickets/{ticket}/complete', [App\Http\Controllers\TicketController::class, 'complete'])->name('tickets.complete');
    Route::post('tickets/{ticket}/update-status', [App\Http\Controllers\TicketController::class, 'updateStatus'])->name('tickets.update-status');
    
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
});

// Public Ticket Request (no auth required)
Route::prefix('public')->name('public.')->group(function () {
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
