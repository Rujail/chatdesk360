@extends('layouts.app')

@section('title', 'Agents')

@section('content')
<div class="body-wrapper mb-0 pg-agent">
    
    <div class="container-fluid mw-100 pb-0">
        <x-breadcrumb title="Agents" /> 

        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row">
                    <div class="col-md-4 col-xl-3">
                        <div class="position-relative">
                            <input
                                type="text"
                                class="form-control datatable-search ps-5"
                                id="input-search"
                                placeholder="Search Contacts..." />
                            <i
                                class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                        </div>
                    </div>
                    <div
                        class="col-md-8 col-xl-9 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <button class="btn btn-primary me-10 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addAgentModal">
                            <i class="ti ti-users text-white me-1 fs-5"></i> Add Agent
                        </button>
                        <a
                            href="javascript:void(0)"
                            id="btn-add-contact"
                            class="btn btn-primary d-flex align-items-center"
                            data-bs-toggle="modal"
                            data-bs-target="#invite-agentmodal">
                            <i class="ti ti-users text-white me-1 fs-5"></i> Invite Agents
                        </a>
                    </div>
                </div>
            </div>
            <div class="card card-body agent-card">
                <div class="table-responsive">
                    <table id="agenttable" class="table datatable-config align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr data-user-id="{{ $user->id }}" class="agent-row">
                                    <td style="width: 50px;">
                                        <input type="checkbox" class="form-check-input row-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initials rounded-circle text-white d-flex align-items-center justify-content-center " style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0 fw-semibold agent-name">{{ $user->name }}</h6>
                                                <span class="text-muted small">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="agt-role">
                                            <span class="badge text-bg-light">{{ $user->role }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="agt-status">
                                            <div class="btn-group">
                                                @php
                                                    $statusBadge = [
                                                        'accepting_chats' => 'bg-success',
                                                        'not_accepting_chats' => 'bg-danger',
                                                        'offline' => 'bg-secondary'
                                                    ][$user->status] ?? 'bg-secondary';
                                                    $statusText = [
                                                        'accepting_chats' => 'Accepting chats',
                                                        'not_accepting_chats' => 'Not accepting chats',
                                                        'offline' => 'Offline'
                                                    ][$user->status] ?? 'Offline';
                                                @endphp
                                                <button class="btn dropdown-toggle status-toggle-btn" type="button" data-bs-toggle="dropdown" data-user-id="{{ $user->id }}">
                                                    <span class="badge {{ $statusBadge }} p-1 me-1"></span>
                                                    <span class="status-label">{{ $statusText }}</span>
                                                </button>
                                                <ul class="dropdown-menu status-menu">
                                                    <li>
                                                        <a class="dropdown-item status-btn" href="javascript:void(0)" data-user-id="{{ $user->id }}" data-status="accepting_chats">
                                                            <span class="badge bg-success p-1 me-1"></span>Accepting chats
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item status-btn" href="javascript:void(0)" data-user-id="{{ $user->id }}" data-status="not_accepting_chats">
                                                            <span class="badge bg-danger p-1 me-1"></span>Not accepting chats
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider" /></li>
                                                    <li>
                                                        <a class="dropdown-item status-btn" href="javascript:void(0)" data-user-id="{{ $user->id }}" data-status="offline">
                                                            <span class="badge bg-secondary p-1 me-1"></span>Logout (Offline)
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="agt-action">
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" class="text-muted" data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots fs-6"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('agents.edit', $user->id) }}">Edit Profile</a></li>
                                                    <li><a class="dropdown-item change-chat-limit-btn" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#chatLimitModal" data-user-id="{{ $user->id }}" data-name="{{ $user->name }}" data-limit="{{ $user->concurrent_chat_limit }}">Change chat limit</a></li>
                                                    <li><a class="dropdown-item view-reports-btn" href="javascript:void(0)" data-name="{{ $user->name }}">View agent reports</a></li>
                                                    
                                                    @if($user->is_suspended)
                                                        <li><a class="dropdown-item text-success activate-agent-btn" href="javascript:void(0)" data-user-id="{{ $user->id }}" data-name="{{ $user->name }}">Activate Agent</a></li>
                                                    @else
                                                        <li><a class="dropdown-item suspend-agent-btn" href="javascript:void(0)" data-user-id="{{ $user->id }}" data-name="{{ $user->name }}">Suspend agent</a></li>
                                                    @endif
                                                    
                                                    <li><hr class="dropdown-divider" /></li>
                                                    <li><a class="dropdown-item text-danger delete-agent-btn" href="javascript:void(0)" data-user-id="{{ $user->id }}" data-name="{{ $user->name }}">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No agents found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OFFCANVAS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="agentDetails" aria-labelledby="agentDetailsLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="agentDetailsLabel">Details</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="accordion accordion-flush" id="agentCardDet">
            
            {{-- Basic Info Accordion --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        <div class="d-flex align-items-center agt-avatar">
                            {{-- ✅ Added id="detail-avatar" --}}
                            <div id="detail-avatar" class="avatar-initials rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"></div>
                            <div class="ms-3">
                                <h6 class="mb-0 fw-semibold agent-name"><span id="detail-name"></span></h6>
                                <span class="text-muted small"><span id="detail-email"></span></span>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne">
                    <div class="accordion-body">
                        <ul class="agt-DetailsCard">
                            <li>Chat Limit: <span id="detail-chat-limit"></span></li>
                            <li>Login status: <span id="detail-status"></span></li>
                            <li>
                                Last seen: 
                                {{-- ✅ Added id="detail-last-seen" --}}
                                <span id="detail-last-seen"><iconify-icon icon="solar:monitor-smartphone-bold" title="MobileAndDesktop"></iconify-icon> Just now</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Groups Accordion --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        <div class="cus-infocard"><h5>Groups</h5></div>
                    </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo">
                    <div class="accordion-body">
                        <div class="d-flex align-items-center gap-1">
                            <span class="g-icon">G</span> <span id="detail-groups"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Performance Accordion --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        <div class="cus-infocard"><h5>Performance</h5></div>
                    </button>
                </h2>
                <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree">
                    <div class="accordion-body">
                        <ul class="agt-performace">
                            {{-- ✅ Added id="detail-total-chats" --}}
                            <li><span class="label"><iconify-icon icon="solar:chat-round-dots-broken"></iconify-icon> Total Chats</span><span class="value" id="detail-total-chats">0</span></li>
                            {{-- ✅ Added id="detail-satisfaction" --}}
                            <li><span class="label"><iconify-icon icon="solar:like-broken"></iconify-icon> Chat satisfaction</span><span class="value" id="detail-satisfaction">0</span></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Invite People -->
<div class="modal fade invite-agentmodal" id="invite-agentmodal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="invite-agent-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h4 class="modal-title" id="myLargeModalLabel">Invite people to 360</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="invite-form-errors" class="alert alert-danger d-none"></div>
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="fs-4">Email Addresses</h5>
                        <div class="form-group"><input type="email" class="form-control" name="email" placeholder="Eg. chatting.agent@domainname.com" /></div>
                        <div class="form-group"><input type="email" class="form-control" name="email2" placeholder="Eg. team.leader@domainname.com" /></div>
                        <div class="form-group"><input type="email" class="form-control" name="email3" placeholder="Eg. marketer@domainname.com" /></div>
                        <div class="form-group"><input type="email" class="form-control" name="email4" placeholder="Eg. sales@domainname.com" /></div>
                    </div>
                    <div class="col-md-4">
                        <h5 class="fs-4">Role</h5>
                        <div class="filters form-group">
                            <select class="custom-selectoption" name="select_role" placeholder="Select Option">
                                <option value="admin">Admin</option>
                                <option value="agent">Agent</option>
                            </select>
                        </div>
                        <div class="filters form-group">
                            <select class="custom-selectoption" name="select_role2" placeholder="Select Option">
                                <option value="admin">Admin</option>
                                <option value="agent">Agent</option>
                            </select>
                        </div>
                        <div class="filters form-group">
                            <select class="custom-selectoption" name="select_role3" placeholder="Select Option">
                                <option value="admin">Admin</option>
                                <option value="agent">Agent</option>
                            </select>
                        </div>
                        <div class="filters form-group">
                            <select class="custom-selectoption" name="select_role4" placeholder="Select Option">
                                <option value="admin">Admin</option>
                                <option value="agent">Agent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-link p-0"><iconify-icon icon="solar:link-bold-duotone"></iconify-icon> Copy invite link</button>
                <button type="button" id="sendInvitesBtn" class="btn bg-primary text-white">Send invites</button>
            </div>
        </div>
    </div>
</div>

<!-- Chat Limit Modal -->
<div class="modal fade chatLimitModal" id="chatLimitModal" tabindex="-1" aria-labelledby="chatLimitModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h4 class="modal-title">Chats Limit (<span id="chat-limit-user-name"></span>)</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group chatbox">
                            <label for="Chat-limit" class="form-label">Concurrent chats</label>
                            <input type="hidden" id="chat-limit-user-id" />
                            <input class="form-control" type="number" id="Chat-limit" name="chat_limit" min="1" value="1" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-start">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn bg-primary text-white" id="saveChatLimitBtn">Apply</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Agent Modal -->
<div class="modal fade" id="addAgentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="addAgentForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="form-errors" class="alert alert-danger d-none"></div>
                    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    {{-- ✅ REMOVED PASSWORD FIELD --}}
                    <small class="text-muted d-block mt-2">A password setup link will be emailed to the agent automatically.</small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {

        // ==========================================
        // 1. ADD AGENT & INVITE AGENTS
        // ==========================================
        $('#addAgentForm').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            let errorDiv = $('#form-errors');
            errorDiv.addClass('d-none').html('');
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

            $.ajax({
                url: "{{ route('agents.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function (response) {
                    $('#submitBtn').prop('disabled', false).html('Save');
                    if (response.status === false) {
                        let errorHtml = '';
                        for (let key in response.errors) { errorHtml += '<p class="mb-0 text-white">' + response.errors[key] + '</p>'; }
                        errorDiv.removeClass('d-none').html(errorHtml);
                    } else {
                        form[0].reset();
                        $('#addAgentModal').modal('hide');
                        Swal.fire('Success!', response.message, 'success').then(() => location.reload());
                    }
                },
                error: function (xhr) {
                    $('#submitBtn').prop('disabled', false).html('Save');
                    if (xhr.status === 422) {
                        let errorHtml = '';
                        for (let key in xhr.responseJSON.errors) { errorHtml += '<p class="mb-0 text-white">' + xhr.responseJSON.errors[key] + '</p>'; }
                        errorDiv.removeClass('d-none').html(errorHtml);
                    } else {
                        Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                    }
                }
            });
        });

        $('#sendInvitesBtn').click(function () {
            let emails = [$('input[name="email"]').val(), $('input[name="email2"]').val(), $('input[name="email3"]').val(), $('input[name="email4"]').val()];
            let roles = [$('select[name="select_role"]').val(), $('select[name="select_role2"]').val(), $('select[name="select_role3"]').val(), $('select[name="select_role4"]').val()];
            let errorDiv = $('#invite-form-errors');
            errorDiv.addClass('d-none').html('');
            let btn = $(this);
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Sending...');

            $.ajax({
                url: "{{ route('agents.invite') }}",
                type: "POST",
                data: { emails: emails, roles: roles, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    btn.prop('disabled', false).html('Send invites');
                    if (response.status === false) {
                        let errorHtml = '';
                        for (let key in response.errors) { errorHtml += '<p class="mb-0 text-white">' + response.errors[key] + '</p>'; }
                        errorDiv.removeClass('d-none').html(errorHtml);
                    } else {
                        $('#invite-agentmodal').modal('hide');
                        $('input[name^="email"]').val('');
                        Swal.fire('Success!', 'Invitations sent successfully!', 'success');
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('Send invites');
                    if (xhr.status === 422) {
                        let errorHtml = '';
                        for (let key in xhr.responseJSON.errors) { errorHtml += '<p class="mb-0 text-white">' + xhr.responseJSON.errors[key] + '</p>'; }
                        errorDiv.removeClass('d-none').html(errorHtml);
                    } else {
                        Swal.fire('Error!', 'An error occurred while sending invites.', 'error');
                    }
                }
            });
        });

        // ==========================================
        // 2. STATUS DROPDOWN FUNCTIONALITY
        // ==========================================
        $('.status-btn').click(function (e) {
            e.preventDefault();
            let userId = $(this).data('user-id');
            let status = $(this).data('status');
            let btn = $(this);
            
            $.ajax({
                url: `/admin/agents/${userId}/status`,
                type: "POST",
                data: { status: status, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.status) {
                        // Update UI immediately
                        let badgeClass = status === 'accepting_chats' ? 'bg-success' : (status === 'not_accepting_chats' ? 'bg-danger' : 'bg-secondary');
                        let statusText = status === 'accepting_chats' ? 'Accepting chats' : (status === 'not_accepting_chats' ? 'Not accepting chats' : 'Offline');
                        
                        let dropdownToggle = btn.closest('.btn-group').find('.status-toggle-btn');
                        dropdownToggle.find('.badge').removeClass('bg-success bg-danger bg-secondary').addClass(badgeClass);
                        dropdownToggle.find('.status-label').text(statusText);

                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Status updated',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Failed to update status.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'An error occurred while updating status.', 'error');
                }
            });
        });

        // ==========================================
        // 3. CHANGE CHAT LIMIT FUNCTIONALITY
        // ==========================================
        $('.change-chat-limit-btn').click(function () {
            let userId = $(this).data('user-id');
            let userName = $(this).data('name');
            let currentLimit = $(this).data('limit');
            
            $('#chat-limit-user-id').val(userId);
            $('#chat-limit-user-name').text(userName);
            $('#Chat-limit').val(currentLimit);
        });

        $('#saveChatLimitBtn').click(function () {
            let userId = $('#chat-limit-user-id').val();
            let newLimit = $('#Chat-limit').val();
            let btn = $(this);
            
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');
            
            $.ajax({
                url: `/admin/agents/${userId}/chat-limit`,
                type: "POST",
                data: { concurrent_chat_limit: newLimit, _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    btn.prop('disabled', false).html('Apply');
                    if (response.status) {
                        $('#chatLimitModal').modal('hide');
                        Swal.fire('Success!', 'Chat limit updated successfully.', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', response.message || 'Failed to update chat limit.', 'error');
                    }
                },
                error: function (xhr) {
                    btn.prop('disabled', false).html('Apply');
                    Swal.fire('Error!', xhr.responseJSON?.message || 'An error occurred.', 'error');
                }
            });
        });

        // ==========================================
        // 4. VIEW AGENT REPORTS
        // ==========================================
        $('.view-reports-btn').click(function () {
            let userName = $(this).data('name');
            Swal.fire('Coming Soon!', 'Reports for ' + userName + ' will be available soon.', 'info');
        });

                // ==========================================
        // 5. SUSPEND AGENT FUNCTIONALITY
        // ==========================================
        $('.suspend-agent-btn').click(function (e) {
            e.preventDefault();
            let userId = $(this).data('user-id');
            let userName = $(this).data('name');
            
            Swal.fire({
                title: 'Suspend Agent?',
                text: `Are you sure you want to suspend ${userName}? They will be immediately logged out and cannot log back in.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c758d',
                confirmButtonText: 'Yes, suspend!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/agents/${userId}/suspend`,
                        type: "POST",
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Suspended!', `${userName} has been suspended.`, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error!', response.errors?.error || 'Failed to suspend agent.', 'error');
                            }
                        }
                    });
                }
            });
        });

        // ==========================================
        // 6. ACTIVATE AGENT FUNCTIONALITY
        // ==========================================
        $('.activate-agent-btn').click(function (e) {
            e.preventDefault();
            let userId = $(this).data('user-id');
            let userName = $(this).data('name');
            
            Swal.fire({
                title: 'Activate Agent?',
                text: `Are you sure you want to activate ${userName}? They will be able to log in again.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c758d',
                confirmButtonText: 'Yes, activate!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/agents/${userId}/activate`,
                        type: "POST",
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Activated!', `${userName} has been activated.`, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error!', 'Failed to activate agent.', 'error');
                            }
                        }
                    });
                }
            });
        });

        // ==========================================
        // 6. DELETE AGENT FUNCTIONALITY
        // ==========================================
        $('.delete-agent-btn').click(function (e) {
            e.preventDefault();
            let userId = $(this).data('user-id');
            let userName = $(this).data('name');
            
            Swal.fire({
                title: 'Delete Agent?',
                text: `Are you sure you want to DELETE ${userName}? This action cannot be undone.`,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c758d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/agents/${userId}`,
                        type: "DELETE",
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function (response) {
                            if (response.status) {
                                Swal.fire('Deleted!', `${userName} has been deleted.`, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error!', response.errors?.error || 'Failed to delete agent.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        }
                    });
                }
            });
        });

        // Clear errors when modals are closed
        $('#addAgentModal, #invite-agentmodal').on('hidden.bs.modal', function () {
            $('#form-errors, #invite-form-errors').addClass('d-none').html('');
        });

        // ==========================================
        // 7. FETCH AGENT DETAILS FOR OFFCANVAS
        // ==========================================
        $('.agent-row').on('click', function (e) {
            // Prevent offcanvas if a dropdown, button, or checkbox was clicked
            if ($(e.target).closest('.dropdown, .dropdown-toggle, .dropdown-menu, .form-check-input, .btn').length) {
                return;
            }

            let userId = $(this).data('user-id');

            // Show offcanvas immediately with a loading state
            let offcanvasEl = document.getElementById('agentDetails');
            let bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            bsOffcanvas.show();

            // Set loading text
            $('#detail-name').text('Loading...');
            $('#detail-email').text('');
            $('#detail-avatar').text('...');
            $('#detail-chat-limit').text('...');
            $('#detail-status').text('...');
            $('#detail-last-seen').text('...');
            $('#detail-groups').text('...');
            $('#detail-total-chats').text('...');
            $('#detail-satisfaction').text('...');

            // Fetch details from backend
            $.ajax({
                url: `/admin/agents/${userId}`, // This hits the agents.show route
                type: "GET",
                success: function (res) {
                    // Calculate initials
                    let names = res.name.split(' ');
                    let initials = names.length >= 2 ? names[0].charAt(0) + names[1].charAt(0) : names[0].charAt(0);

                    // Populate basic info
                    $('#detail-name').text(res.name);
                    $('#detail-email').text(res.email);
                    $('#detail-avatar').text(initials.toUpperCase());

                    // Populate details list
                    $('#detail-chat-limit').text(res.chat_limit);
                    $('#detail-status').text(res.status.replace(/_/g, ' '));
                    $('#detail-last-seen').text(res.last_seen);

                    // Populate groups
                    $('#detail-groups').text(res.groups || 'No groups assigned');

                    // Populate performance
                    $('#detail-total-chats').text(res.total_chats);
                    $('#detail-satisfaction').text(res.satisfaction);

                    // Set Avatar Color dynamically
                    let colors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#0dcaf0'];
                    let charCodeSum = res.name.charCodeAt(0) + (res.name.charCodeAt(1) || 0);
                    let color = colors[charCodeSum % colors.length];
                    $('#detail-avatar').css('background-color', color);
                },
                error: function () {
                    Swal.fire('Error!', 'Failed to load agent details.', 'error');
                    bsOffcanvas.hide();
                }
            });
        });

    });
</script>
@endpush