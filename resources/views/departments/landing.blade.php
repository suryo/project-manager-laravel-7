@extends('layouts.guest')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Header/Hero Section for Department --}}
            <div class="text-center mb-5">
                <div class="mb-3">
                    <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
                        <i class="bi bi-building me-1"></i> Department Portal
                    </span>
                </div>
                <h1 class="display-4 fw-bold text-dark mb-3">{{ $department->name }}</h1>
                <div class="d-flex justify-content-center">
                    <div style="width: 60px; height: 4px; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); border-radius: 2px;"></div>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="card shadow-lg border-0 rounded-lg overflow-hidden glass-card">
                <div class="card-body p-5">
                    
                    {{-- Navigation Menu (Modal Based) --}}
                    <div class="row g-4 mb-5">
                        <div class="col-md-3 col-6">
                            <button class="btn btn-white w-100 py-4 border-3 border-dark rounded-0 fw-bold text-uppercase shadow-btn hover-scale" 
                                    data-bs-toggle="modal" data-bs-target="#modalAboutUs">
                                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                About Us
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button class="btn btn-white w-100 py-4 border-3 border-dark rounded-0 fw-bold text-uppercase shadow-btn hover-scale" 
                                    data-bs-toggle="modal" data-bs-target="#modalTeamMembers">
                                <i class="bi bi-people fs-3 d-block mb-2"></i>
                                Team Members
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button class="btn btn-white w-100 py-4 border-3 border-dark rounded-0 fw-bold text-uppercase shadow-btn hover-scale" 
                                    data-bs-toggle="modal" data-bs-target="#modalRecentMeetings">
                                <i class="bi bi-calendar-event fs-3 d-block mb-2"></i>
                                Meetings
                            </button>
                        </div>
                        <div class="col-md-3 col-6">
                            <button class="btn btn-white w-100 py-4 border-3 border-dark rounded-0 fw-bold text-uppercase shadow-btn hover-scale" 
                                    data-bs-toggle="modal" data-bs-target="#modalRecentProjects">
                                <i class="bi bi-kanban fs-3 d-block mb-2"></i>
                                Projects
                            </button>
                        </div>
                    </div>


                    <hr class="my-5 border-light">

                    {{-- Actions / Ticket Request --}}
                    <div class="text-center">
                        <h4 class="fw-bold mb-4">Need Assistance?</h4>
                        <p class="text-muted mb-4">Submit a ticket directly to {{ $department->name }} team.</p>
                        
                        <a href="{{ route('public.ticket-request', ['department_id' => $department->id]) }}" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm hover-scale">
                            <i class="bi bi-ticket-perforated me-2"></i>Submit Ticket
                        </a>
                        
                        @guest
                        <div class="mt-4">
                            <p class="text-muted small">
                                <i class="bi bi-info-circle me-1"></i>
                                <a href="{{ route('login') }}" class="text-decoration-none">Login</a> to view team members and meetings
                            </p>
                        </div>
                        @endguest
                        
                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="text-decoration-none text-muted small">
                                <i class="bi bi-arrow-left me-1"></i>Back to Home
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            {{-- FLOATING LIVE CHAT WIDGET展 --}}
            <div id="chat-widget-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050; display: flex; flex-direction: column; align-items: flex-end;">
                
                {{-- Chat Window (Hidden by default) --}}
                <div id="chat-popup" class="card d-none" style="width: 380px; box-shadow: 8px 8px 0 #000; border: 3px solid #000 !important; border-radius: 0 !important; margin-bottom: 15px;">
                    <div class="card-header py-3 px-4 bg-white border-bottom border-3 border-dark d-flex justify-content-between align-items-center rounded-0">
                        <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1 h6">
                            <i class="bi bi-chat-dots me-2"></i> <span>Department Chat</span>
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success border border-1 border-dark text-uppercase" style="font-size: 0.6rem;" id="online-count">0 Online</span>
                            <button type="button" class="btn-close" id="close-chat" style="font-size: 0.7rem;"></button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-0">
                            {{-- Chat Area --}}
                            <div class="col-12">
                                <div class="chat-messages p-3 bg-light" style="height: 350px; overflow-y: auto;" id="chat-box">
                                    <div class="text-center text-muted mt-5">Loading chat...</div>
                                </div>
                                <div class="p-3 bg-white border-top border-3 border-dark">
                                    @auth
                                        <form id="chat-form" class="d-flex gap-2">
                                            <input type="text" class="form-control border-2 border-dark rounded-0 shadow-none" id="message-input" placeholder="Type a message..." required>
                                            <button type="submit" class="btn btn-primary border-2 border-dark rounded-0 fw-bold text-uppercase px-3 py-1" style="box-shadow: 3px 3px 0 #000; font-size: 0.8rem;">
                                                Send
                                            </button>
                                        </form>
                                    @else
                                        <div id="guest-chat-input" style="display: none;">
                                            <form id="chat-form" class="d-flex gap-2">
                                                <input type="text" class="form-control border-2 border-dark rounded-0 shadow-none" id="message-input" placeholder="Type a message..." required>
                                                <button type="submit" class="btn btn-primary border-2 border-dark rounded-0 fw-bold text-uppercase px-3 py-1" style="box-shadow: 3px 3px 0 #000; font-size: 0.8rem;">
                                                    Send
                                                </button>
                                            </form>
                                            <div class="text-end mt-1">
                                                <small class="text-muted" style="font-size: 0.7rem;">Chatting as <span id="guest-display-name" class="fw-bold">Guest</span></small>
                                            </div>
                                        </div>

                                        <div id="guest-info-form">
                                            <form id="guest-login-form">
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm border-2 border-dark rounded-0" id="guest-name" placeholder="Name" required>
                                                </div>
                                                <div class="mb-2">
                                                    <input type="email" class="form-control form-control-sm border-2 border-dark rounded-0" id="guest-email" placeholder="Email" required>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <input type="text" class="form-control form-control-sm border-2 border-dark rounded-0" id="guest-contact" placeholder="Contact/Phone" required>
                                                    <button type="submit" class="btn btn-dark btn-sm border-2 border-dark rounded-0 fw-bold text-uppercase px-3">
                                                        Join
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                            {{-- Online Members (Hidden in mobile/small popup, or simplified) --}}
                            <div class="col-12 bg-light border-top border-dark p-2 d-none" id="online-members-container">
                                <div class="d-flex flex-wrap gap-2 px-2" id="online-members-list">
                                    {{-- Member icons will go here --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Toggle Button --}}
                <button id="chat-toggle" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center border-3 border-dark p-0" 
                        style="width: 65px; height: 65px; box-shadow: 6px 6px 0 #000 !important; cursor: pointer; transition: all 0.2s ease; background: #667eea !important;">
                    <i class="bi bi-chat-dots-fill text-white" style="font-size: 2rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-dark" id="chat-notification" style="display: none; box-shadow: 2px 2px 0 #000;">
                        !
                    </span>
                </button>
            </div>

            {{-- MODALS --}}
            
            {{-- About Us Modal --}}
            <div class="modal fade" id="modalAboutUs" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-3 border-dark rounded-0 shadow-lg" style="box-shadow: 10px 10px 0 #000 !important;">
                        <div class="modal-header border-bottom border-3 border-dark bg-white rounded-0">
                            <h5 class="modal-title fw-bold text-uppercase letter-spacing-1 h6">
                                <i class="bi bi-info-circle me-2 text-primary"></i>About {{ $department->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="ql-snow">
                                <div class="ql-editor" style="padding: 0; min-height: auto; font-size: 0.9rem;">
                                    @if($department->description)
                                        {!! $department->description !!}
                                    @else
                                        <em class="text-muted">No description currently available for this department.</em>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top border-3 border-dark bg-light rounded-0">
                            <button type="button" class="btn btn-dark rounded-0 fw-bold px-4 border-2 border-dark shadow-btn-sm" data-bs-dismiss="modal">CLOSE</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Team Members Modal --}}
            <div class="modal fade" id="modalTeamMembers" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-3 border-dark rounded-0 shadow-lg" style="box-shadow: 10px 10px 0 #000 !important;">
                        <div class="modal-header border-bottom border-3 border-dark bg-white rounded-0">
                            <h5 class="modal-title fw-bold text-uppercase letter-spacing-1 h6">
                                <i class="bi bi-people me-2 text-primary"></i>Team Members
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            @auth
                                @if($department->members && $department->members->count() > 0)
                                    <div class="row">
                                        @foreach($department->members as $member)
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-2 border-dark rounded-0 shadow-btn-sm h-100 p-1">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">{{ $member->name }}</h6>
                                                        @if($member->pivot->role)
                                                            <span class="badge bg-primary border border-dark rounded-0 text-uppercase" style="font-size: 0.55rem;">{{ $member->pivot->role }}</span>
                                                        @endif
                                                        <p class="text-muted small mb-0 mt-2" style="font-size: 0.75rem;">{{ $member->email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">No team members listed yet.</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-lock fs-1 text-muted d-block mb-3"></i>
                                    <p class="fw-bold mb-3">Please login to view team members.</p>
                                    <a href="{{ route('login') }}" class="btn btn-primary rounded-0 border-2 border-dark fw-bold px-4 shadow-btn">LOGIN NOW</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Meetings Modal --}}
            <div class="modal fade" id="modalRecentMeetings" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-3 border-dark rounded-0 shadow-lg" style="box-shadow: 10px 10px 0 #000 !important;">
                        <div class="modal-header border-bottom border-3 border-dark bg-white rounded-0 py-3">
                            <h5 class="modal-title fw-bold text-uppercase letter-spacing-1 h6">
                                <i class="bi bi-calendar-event me-2 text-primary"></i>Recent Meetings
                            </h5>
                            @auth
                            <a href="{{ route('department.meeting.create', $department->slug) }}" class="btn btn-sm btn-outline-dark rounded-0 border-2 ms-auto me-3 fw-bold text-uppercase shadow-btn-sm" style="font-size: 0.7rem;">
                                <i class="bi bi-plus-circle me-1"></i>New Meeting
                            </a>
                            @endauth
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            @auth
                                @if($department->meetings && $department->meetings->count() > 0)
                                    <div class="list-group rounded-0 gap-3">
                                        @foreach($department->meetings as $meeting)
                                            <a href="{{ route('department.meeting.show', [$department->slug, $meeting->id]) }}" class="list-group-item list-group-item-action border-2 border-dark mb-2 shadow-btn-sm p-3 rounded-0">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <h6 class="mb-1 fw-bold" style="font-size: 0.9rem;">{{ $meeting->title }}</h6>
                                                    <span class="badge bg-white text-dark border border-dark rounded-0" style="font-size: 0.65rem;">{{ $meeting->meeting_date->format('M d, Y') }}</span>
                                                </div>
                                                @if($meeting->description)
                                                    <p class="mb-2 text-muted small mt-2" style="font-size: 0.8rem;">{{ Str::limit($meeting->description, 100) }}</p>
                                                @endif
                                                @if($meeting->location)
                                                    <div class="mt-2">
                                                        <small class="text-dark fw-bold" style="font-size: 0.7rem;">
                                                            <i class="bi bi-geo-alt me-1 text-primary"></i>{{ $meeting->location }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-4">
                                        <a href="{{ route('meetings.index', ['department_id' => $department->id]) }}" class="btn btn-dark border-2 border-dark rounded-0 fw-bold px-4 shadow-btn">
                                            VIEW ALL MEETINGS
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted">No meetings scheduled yet.</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-lock fs-1 text-muted d-block mb-3"></i>
                                    <p class="fw-bold mb-3">Please login to view recent meetings.</p>
                                    <a href="{{ route('login') }}" class="btn btn-primary rounded-0 border-2 border-dark fw-bold px-4 shadow-btn">LOGIN NOW</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Projects Modal --}}
            <div class="modal fade" id="modalRecentProjects" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content border-3 border-dark rounded-0 shadow-lg" style="box-shadow: 10px 10px 0 #000 !important;">
                        <div class="modal-header border-bottom border-3 border-dark bg-white rounded-0">
                            <h5 class="modal-title fw-bold text-uppercase letter-spacing-1 h6">
                                <i class="bi bi-kanban me-2 text-primary"></i>Recent Projects
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            @auth
                                @if(isset($department->recent_projects) && $department->recent_projects->count() > 0)
                                    <div class="row">
                                        @foreach($department->recent_projects as $project)
                                            <div class="col-md-6 mb-3">
                                                <div class="card h-100 border-2 border-dark rounded-0 shadow-btn-sm hover-scale p-1">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">
                                                                <a href="{{ route('department.projects.show', ['slug' => $department->slug, 'projectSlug' => $project->slug]) }}" class="text-decoration-none text-dark stretched-link">{{ $project->title }}</a>
                                                            </h6>
                                                            @if($project->status)
                                                                <span class="badge border border-dark rounded-0 text-uppercase" style="background-color: {{ $project->status->color }}; color: #fff; font-size: 0.55rem;">
                                                                    {{ $project->status->name }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if($project->description)
                                                            <p class="text-muted small mb-4 text-truncate-2" style="font-size: 0.75rem;">{{ Str::limit($project->description, 80) }}</p>
                                                        @endif
                                                        
                                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                                            <small class="text-dark fw-bold" style="font-size: 0.65rem;">
                                                                <i class="bi bi-person me-1 text-primary"></i>{{ $project->user->name ?? 'Unknown' }}
                                                            </small>
                                                            @if($project->end_date)
                                                                <small class="{{ $project->end_date->isPast() ? 'text-danger' : 'text-success' }} fw-bold" style="font-size: 0.65rem;">
                                                                    <i class="bi bi-calendar-check me-1"></i>Due: {{ $project->end_date->format('M d') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">No active projects found for this department's team.</p>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-lock fs-1 text-muted d-block mb-3"></i>
                                    <p class="fw-bold mb-3">Please login to view active projects.</p>
                                    <a href="{{ route('login') }}" class="btn btn-primary rounded-0 border-2 border-dark fw-bold px-4 shadow-btn">LOGIN NOW</a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Specific styles for this landing page if needed, normally inherited from guest */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 4px solid #000 !important;
        border-radius: 0 !important;
        box-shadow: 15px 15px 0 rgba(0,0,0,0.1) !important;
    }
    .hover-scale {
        transition: all 0.2s ease;
    }
    .hover-scale:hover {
        transform: translate(-3px, -3px);
        box-shadow: 8px 8px 0 #000 !important;
    }
    .shadow-btn {
        box-shadow: 6px 6px 0 #000 !important;
        transition: all 0.2s ease;
    }
    .shadow-btn:active {
        transform: translate(3px, 3px);
        box-shadow: 2px 2px 0 #000 !important;
    }
    .shadow-btn-sm {
        box-shadow: 4px 4px 0 #000 !important;
        transition: all 0.2s ease;
    }
    .shadow-btn-sm:active {
        transform: translate(2px, 2px);
        box-shadow: 1px 1px 0 #000 !important;
    }
    .letter-spacing-1 {
        letter-spacing: 1px;
    }
    .btn-white {
        background: #fff;
        color: #000;
        border: 3px solid #000;
    }
    .btn-white:hover {
        background: #f8f9fa;
        color: #000;
    }
    .truncate-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Override ql-editor default fonts to match theme */
    .ql-editor { font-family: inherit; }
    .ql-editor p { margin-bottom: 1rem; }
    .ql-editor ol, .ql-editor ul { padding-left: 1.5rem; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slug = "{{ $department->slug }}";
    const chatBox = document.getElementById('chat-box');
    const onlineList = document.getElementById('online-members-list');
    const onlineCount = document.getElementById('online-count');
    const chatForm = document.getElementById('chat-form');
    const msgInput = document.getElementById('message-input');
    const chatPopup = document.getElementById('chat-popup');
    const chatToggle = document.getElementById('chat-toggle');
    const closeChat = document.getElementById('close-chat');
    const chatNotification = document.getElementById('chat-notification');

    // UI Elements for Staff View
    const chatHeaderTitle = chatPopup.querySelector('.card-header h5');
    const chatHeaderExtra = chatPopup.querySelector('.card-header .d-flex.align-items-center.gap-2');
    
    // Add Back Button for Staff (initially hidden)
    const backBtn = document.createElement('button');
    backBtn.className = 'btn btn-sm btn-outline-dark border-2 rounded-0 fw-bold d-none me-2';
    backBtn.innerHTML = '<i class="bi bi-arrow-left"></i>';
    backBtn.style.padding = '2px 5px';
    chatHeaderTitle.prepend(backBtn);

    // Guest Handling
    const guestForm = document.getElementById('guest-login-form');
    const guestInfoDiv = document.getElementById('guest-info-form');
    const guestInputDiv = document.getElementById('guest-chat-input');
    const guestDisplayName = document.getElementById('guest-display-name');
    
    let guestData = null;
    try {
        const stored = localStorage.getItem('chat_guest_' + slug);
        if(stored) guestData = JSON.parse(stored);
    } catch(e) { console.error('Error parsing guest data', e); }

    @php
        $isDeptStaff = auth()->check() && $department->members->contains(auth()->user());
    @endphp
    const isDeptStaff = {{ $isDeptStaff ? 'true' : 'false' }};
    let conversationId = null;
    const isAuth = {{ auth()->check() ? 'true' : 'false' }};
    const currentUserId = {{ auth()->id() ?? 'null' }};

    function generateUUID() {
        return 'conv_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
    }

    if (!isAuth) {
        conversationId = localStorage.getItem('chat_conv_id_' + slug);
        if (!conversationId) {
            conversationId = generateUUID();
            localStorage.setItem('chat_conv_id_' + slug, conversationId);
        }
    } else {
        // For regular users, use their ID as part of conv ID to persist across sessions
        conversationId = 'user_' + currentUserId + '_' + slug;
    }

    if (guestData && !isAuth) {
        if(guestInfoDiv) guestInfoDiv.style.display = 'none';
        if(guestInputDiv) guestInputDiv.style.display = 'block';
        if(guestDisplayName) guestDisplayName.innerText = guestData.name;
    }

    if (guestForm) {
        guestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('guest-name').value;
            const email = document.getElementById('guest-email').value;
            const contact = document.getElementById('guest-contact').value;
            
            guestData = { name, email, contact };
            localStorage.setItem('chat_guest_' + slug, JSON.stringify(guestData));
            
            guestInfoDiv.style.display = 'none';
            guestInputDiv.style.display = 'block';
            guestDisplayName.innerText = name;
            fetchMessages(); 
        });
    }

    let isChatOpen = false;
    let pollInterval = null;
    let isStaffMode = false;
    let activeConversationId = isDeptStaff ? null : conversationId; // Staff starts in inbox (null), others in their private thread

    function toggleChat() {
        isChatOpen = !isChatOpen;
        if (isChatOpen) {
            chatPopup.classList.remove('d-none');
            chatNotification.style.display = 'none';
            fetchMessages();
            startPolling();
        } else {
            chatPopup.classList.add('d-none');
            stopPolling();
        }
    }

    function startPolling() {
        if (!pollInterval) pollInterval = setInterval(fetchMessages, 5000);
    }

    function stopPolling() {
        if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
    }

    if (chatToggle) chatToggle.addEventListener('click', toggleChat);
    if (closeChat) closeChat.addEventListener('click', toggleChat);

    backBtn.addEventListener('click', function() {
        activeConversationId = null;
        backBtn.classList.add('d-none');
        chatHeaderTitle.querySelector('span:last-child').innerText = ' Department Chat';
        const inputArea = chatPopup.querySelector('.p-3.bg-white.border-top.border-3.border-dark');
        inputArea.style.display = 'none';
        fetchMessages();
    });

    function fetchMessages() {
        if (!isChatOpen && !isDeptStaff && !conversationId) return; // Nothing to check yet

        let url = `{!! route('department.chat.fetch', ['slug' => ':slug']) !!}`.replace(':slug', slug);
        if (activeConversationId) {
            url += '?conversation_id=' + activeConversationId;
        }
        
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                isStaffMode = data.is_staff;
                const inputArea = chatPopup.querySelector('.p-3.bg-white.border-top.border-3.border-dark');
                
                if (isStaffMode && !activeConversationId) {
                    if (isChatOpen) renderInbox(data.conversations);
                    if(inputArea) inputArea.style.display = 'none';

                    // For staff: total unread is sum of all conversation unread counts
                    const totalUnread = data.conversations.reduce((acc, conv) => acc + (conv.unread_count || 0), 0);
                    if (totalUnread > 0) {
                        chatNotification.style.display = 'block';
                        chatNotification.innerText = totalUnread;
                    } else {
                        chatNotification.style.display = 'none';
                    }
                } else if (data.messages) {
                    if (isChatOpen) renderMessages(data.messages);
                    if(inputArea) inputArea.style.display = 'block';

                    // For clients: data.total_unread contains the count
                    if (!isStaffMode) {
                        if (data.total_unread > 0) {
                            chatNotification.style.display = 'block';
                            chatNotification.innerText = data.total_unread;
                        } else {
                            chatNotification.style.display = 'none';
                        }
                    } else if (activeConversationId) {
                        // Staff viewing a conversation: no global badge needed on the main button if open?
                        // Actually, if it's open, the badge is redundant but doesn't hurt.
                        // For consistency, let's hide it if staff is actively in a thread.
                        chatNotification.style.display = 'none';
                    }
                }
                
                if(data.online_members && isChatOpen) renderOnline(data.online_members);
            })
            .catch(error => console.error('Fetch error:', error));
    }

    function renderInbox(conversations) {
        let html = '<div class="list-group list-group-flush rounded-0">';
        chatHeaderTitle.querySelector('span:last-child').innerText = ' Chat Inbox';
        
        if (!conversations || conversations.length === 0) {
            html += '<div class="text-center text-muted p-5">No active conversations.</div>';
        } else {
            conversations.forEach(conv => {
                const name = conv.user ? conv.user.name : (conv.guest_name ? conv.guest_name + ' (Guest)' : 'Guest');
                const lastMsg = conv.message ? conv.message.substring(0, 30) + (conv.message.length > 30 ? '...' : '') : 'No messages';
                const time = new Date(conv.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const unreadBadge = conv.unread_count > 0 ? `<span class="badge bg-danger border border-dark rounded-circle ms-2" style="font-size: 0.6rem; padding: 0.3em 0.5em;">${conv.unread_count}</span>` : '';
                
                html += `
                    <button class="list-group-item list-group-item-action border-bottom border-dark p-3 conversation-item ${conv.unread_count > 0 ? 'bg-light fw-bold' : ''}" data-id="${conv.conversation_id}" data-name="${name}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <h6 class="fw-bold mb-0" style="font-size: 0.85rem;">${name}</h6>
                                ${unreadBadge}
                            </div>
                            <small class="text-muted" style="font-size: 0.65rem;">${time}</small>
                        </div>
                        <p class="mb-0 text-muted small truncate-1">${lastMsg}</p>
                    </button>
                `;
            });
        }
        html += '</div>';
        chatBox.innerHTML = html;
        chatBox.scrollTop = 0;

        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', function() {
                activeConversationId = this.dataset.id;
                const name = this.dataset.name;
                backBtn.classList.remove('d-none');
                chatHeaderTitle.querySelector('span:last-child').innerText = ' Chat: ' + name;
                fetchMessages();
            });
        });
    }

    function renderMessages(messages) {
        let html = '';
        if(messages.length === 0) {
            html = '<div class="text-center text-muted mt-5">No messages yet.</div>';
        } else {
            messages.forEach(msg => {
                const isMe = (msg.is_from_staff && isStaffMode) || 
                             (!msg.is_from_staff && !isStaffMode && ((msg.user_id && msg.user_id === currentUserId) || (msg.guest_email && guestData && msg.guest_email === guestData.email)));
                
                // If I am staff, and message is from staff, it's me.
                // If I am guest, and message is NOT from staff, it's me (comparing IDs).
                // Simplified isMe for private thread context:
                const isActuallyMe = (isStaffMode && msg.is_from_staff) || (!isStaffMode && !msg.is_from_staff);

                const senderName = msg.user ? msg.user.name : (msg.guest_name ? msg.guest_name : 'Guest');
                const avatarLetter = senderName.charAt(0).toUpperCase();
                const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                html += `
                    <div class="d-flex mb-3 ${isActuallyMe ? 'justify-content-end' : ''}">
                        ${!isActuallyMe ? `<div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2 border border-1 border-dark shadow-none" style="width: 28px; height: 28px; font-size: 0.7rem;">${avatarLetter}</div>` : ''}
                        <div class="${isActuallyMe ? 'bg-primary text-white' : 'bg-white border border-2 border-dark'} p-2 rounded-0 shadow-btn-sm" style="max-width: 80%;">
                            <div class="d-flex justify-content-between align-items-center mb-1 gap-3">
                                <small class="fw-bold ${isActuallyMe ? 'text-white' : 'text-dark'}" style="font-size: 0.65rem;">${senderName}</small>
                                <small class="${isActuallyMe ? 'text-white-50' : 'text-muted'}" style="font-size: 0.6rem;">${time}</small>
                            </div>
                            <p class="mb-0" style="word-wrap: break-word; font-size: 0.8rem;">${msg.message}</p>
                        </div>
                    </div>
                `;
            });
        }
        
        if(chatBox.innerHTML !== html) {
             chatBox.innerHTML = html;
             chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    function renderOnline(members) {
        onlineCount.innerText = members.length + ' Online';
        let html = '';
        members.forEach(member => {
            const letter = member.name.charAt(0).toUpperCase();
            html += `<div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center border border-1 border-dark position-relative" style="width: 24px; height: 24px; font-size: 0.6rem;" title="${member.name}">${letter}<span class="position-absolute bottom-0 end-0 bg-white border border-dark rounded-circle" style="width: 6px; height: 6px;"></span></div>`;
        });
        onlineList.innerHTML = html;
        const container = document.getElementById('online-members-container');
        members.length > 0 ? container.classList.remove('d-none') : container.classList.add('d-none');
    }

    if(chatForm){
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = msgInput.value;
            if(!message.trim() || !activeConversationId) return;
            
            let payload = {
                message: message,
                conversation_id: activeConversationId,
                _token: "{{ csrf_token() }}"
            };
            
            if(!isAuth) {
                 if(!guestData) { alert("Please enter guest info first."); return; }
                 payload.guest_name = guestData.name;
                 payload.guest_email = guestData.email;
                 payload.guest_contact = guestData.contact;
            }

            fetch(`{!! route('department.chat.send', ['slug' => ':slug']) !!}`.replace(':slug', slug), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) { msgInput.value = ''; fetchMessages(); }
            })
            .catch(err => console.error('Send error:', err));
        });
    }

    // Start background poll for notifications even if closed
    setInterval(fetchMessages, 10000); 
    // Initial fetch to show current unread state
    fetchMessages();
});
</script>
@endpush
@endsection
