@extends('layouts.app')

@section('title', 'Shortcut')

@section('content')
<div class="body-wrapper mb-0 pg-shortcut">
    <div class="container-fluid mw-100 pb-0">
        <x-breadcrumb title="Shortcut" /> 

        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <a href="javascript:void(0)" id="btn-ban-customer" class="btn btn-primary d-inline align-items-center gap-1"
                           data-bs-toggle="modal" data-bs-target="#shortcutModal">
                            <i class="ti ti-plus"></i> New Shortcut
                        </a>
                    </div>
                </div>

                <!-- Tabs + Search -->
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 shortcut-tabs">
                    <ul class="nav nav-tabs" id="shortcutTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">All</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="shared-tab" data-bs-toggle="tab" data-bs-target="#shared" type="button" role="tab">Shared</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="private-tab" data-bs-toggle="tab" data-bs-target="#private" type="button" role="tab">Private</button>
                        </li>
                    </ul>
                    <div class="ms-auto">
                        <input type="text" class="form-control" id="shortcut-search-input" placeholder="Search ...">
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content shortcut-tab" id="shortcutTabsContent">

                    <!-- All -->
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        <div class="total-shortcut">{{ count($shortcuts) }} canned responses</div>
                        <div class="shortcut-lists">
                            @forelse($shortcuts as $s)
                            <div class="shortcut-list" 
                                 data-id="{{ $s->id }}" 
                                 data-shortcut="{{ $s->shortcut }}" 
                                 data-response="{{ $s->response_text }}" 
                                 data-tags="{{ $s->tags ? json_encode($s->tags) : '[]' }}" 
                                 data-auto="{{ $s->auto_apply_tags ? 'true' : 'false' }}"
                                 data-shared="{{ $s->is_shared ? 'true' : 'false' }}">
                                <div class="hastag-short">{{ $s->shortcut }}</div>
                                <div class="shortcut-msg"><p>{{ Str::limit($s->response_text, 100) }}</p></div>
                                <div class="shortcut-info">
                                    <p>{{ $s->is_shared ? 'Shared' : 'Private' }}, added {{ $s->created_at->format('d F Y') }}</p>
                                </div>
                                <div class="short-btns">
                                    <a href="javascript:void(0)" class="editShortcut" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" data-shortcutid="{{ $s->id }}">
                                        <i class="fs-4 ti ti-edit"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="deletShortcut" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-shortcutid="{{ $s->id }}">
                                        <i class="fs-4 ti ti-trash"></i>
                                    </a>
                                </div>
                            </div>
                            @empty
                            <div class="p-3 border rounded text-center text-muted">No shortcuts yet</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Shared -->
                    <div class="tab-pane fade" id="shared" role="tabpanel">
                        <div class="total-shortcut">{{ $shortcuts->where('is_shared', true)->count() }} shared canned responses</div>
                        <div class="shortcut-lists">
                            @forelse($shortcuts->where('is_shared', true) as $s)
                            <div class="shortcut-list" data-id="{{ $s->id }}" data-shortcut="{{ $s->shortcut }}" data-response="{{ $s->response_text }}" data-tags="{{ $s->tags ? json_encode($s->tags) : '[]' }}" data-auto="{{ $s->auto_apply_tags ? 'true' : 'false' }}" data-shared="true">
                                <div class="hastag-short">{{ $s->shortcut }}</div>
                                <div class="shortcut-msg"><p>{{ Str::limit($s->response_text, 100) }}</p></div>
                                <div class="shortcut-info"><p>Shared, added {{ $s->created_at->format('d F Y') }}</p></div>
                                <div class="short-btns">
                                    <a href="javascript:void(0)" class="editShortcut" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" data-shortcutid="{{ $s->id }}"><i class="fs-4 ti ti-edit"></i></a>
                                    <a href="javascript:void(0)" class="deletShortcut" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-shortcutid="{{ $s->id }}"><i class="fs-4 ti ti-trash"></i></a>
                                </div>
                            </div>
                            @empty
                            <div class="p-3 border rounded text-center text-muted"><strong>No shared shortcuts yet</strong></div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Private -->
                    <div class="tab-pane fade" id="private" role="tabpanel">
                        <div class="total-shortcut">{{ $shortcuts->where('is_shared', false)->count() }} private canned responses</div>
                        <div class="shortcut-lists">
                            @forelse($shortcuts->where('is_shared', false) as $s)
                            <div class="shortcut-list" data-id="{{ $s->id }}" data-shortcut="{{ $s->shortcut }}" data-response="{{ $s->response_text }}" data-tags="{{ $s->tags ? json_encode($s->tags) : '[]' }}" data-auto="{{ $s->auto_apply_tags ? 'true' : 'false' }}" data-shared="false">
                                <div class="hastag-short">{{ $s->shortcut }}</div>
                                <div class="shortcut-msg"><p>{{ Str::limit($s->response_text, 100) }}</p></div>
                                <div class="shortcut-info"><p>Private, added {{ $s->created_at->format('d F Y') }}</p></div>
                                <div class="short-btns">
                                    <a href="javascript:void(0)" class="editShortcut" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" data-shortcutid="{{ $s->id }}"><i class="fs-4 ti ti-edit"></i></a>
                                    <a href="javascript:void(0)" class="deletShortcut" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-shortcutid="{{ $s->id }}"><i class="fs-4 ti ti-trash"></i></a>
                                </div>
                            </div>
                            @empty
                            <div class="p-3 border rounded text-center text-muted"><strong>No private shortcuts yet</strong></div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Shortcut Modal -->
<div class="modal fade" id="shortcutModal" tabindex="-1" aria-labelledby="shortcutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shortcutModalLabel">Add New Shortcut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Response Text -->
                <div class="mb-4 shortcut-textarea">
                    <label class="form-label fw-semibold">Response text</label>
                    <textarea id="response-text" class="form-control response-text" rows="6" placeholder="Enter your response..."></textarea>
                    <div class="shortcut-placeholder">
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" id="variableDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <iconify-icon icon="solar:square-share-line-broken"></iconify-icon>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="variableDropdown">
                                <li><a class="dropdown-item variable-item" href="#" data-value="%customer-email%">Customer Email</a></li>
                                <li><a class="dropdown-item variable-item" href="#" data-value="%customer-name%">Customer Name</a></li>
                                <li><a class="dropdown-item variable-item" href="#" data-value="%agent-name%">Agent Name</a></li>
                                <li><a class="dropdown-item variable-item" href="#" data-value="%agent-email%">Agent Email</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Shortcuts -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Shortcuts</label>
                    <div id="shortcut-tags-box">
                        <input type="text" name="shortcuts" class="form-control shortcut-input" placeholder="Type shortcut and hit Enter">
                    </div>
                    <small class="text-muted">Use shortcuts like: <code>#call</code>, <code>#greeting</code></small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="assign-tags-input">Assign tags <span class="text-muted">(0/10 tags)</span></label>
                    <input id="assign-tags-input" class="form-control assign-tags-input" placeholder="Select tags..." />
                    <small class="text-muted d-block mt-1">Select tags (max 10)</small>
                </div>

                <!-- Shared Toggle -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="sharedCheck">
                    <label class="form-check-label" for="sharedCheck">Share this shortcut with all agents</label>
                </div>

                <!-- Auto assign toggle -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="autoAssignCheck">
                    <label class="form-check-label" for="autoAssignCheck">Automatically assign tags to the chat in which canned was used</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-save-shortcut">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Shortcut Modal -->
<div class="modal fade" id="EditshortcutModal" tabindex="-1" aria-labelledby="EditshortcutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditshortcutModalLabel">Edit Shortcut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Response Text -->
                <div class="mb-4 shortcut-textarea">
                    <label class="form-label fw-semibold">Response text</label>
                    <textarea id="edit-response-text" class="form-control response-text" rows="6" placeholder="Enter your response..."></textarea>
                    <div class="shortcut-placeholder">
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <iconify-icon icon="solar:square-share-line-broken"></iconify-icon>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item variable-item" href="#" data-value="%customer-email%">Customer Email</a></li>
                                <li><a class="dropdown-item variable-item" href="#" data-value="%customer-name%">Customer Name</a></li>
                                <li><a class="dropdown-item variable-item" href="#" data-value="%agent-name%">Agent Name</a></li>
                                <li><a class="dropdown-item variable-item" href="#" data-value="%agent-email%">Agent Email</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Shortcuts -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Shortcuts</label>
                    <div>
                        <input type="text" name="edit-shortcuts" class="form-control shortcut-input" placeholder="Type shortcut and hit Enter">
                    </div>
                    <small class="text-muted">Use shortcuts like: <code>#call</code>, <code>#greeting</code></small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold" for="edit-assign-tags-input">Assign tags <span class="text-muted edit-tag-count">(0/10 tags)</span></label>
                    <input id="edit-assign-tags-input" class="form-control assign-tags-input" placeholder="Select tags..." />
                    <small class="text-muted d-block mt-1">Select tags (max 10)</small>
                </div>

                <!-- Shared Toggle -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="editSharedCheck">
                    <label class="form-check-label" for="editSharedCheck">Share this shortcut with all agents</label>
                </div>

                <!-- Auto assign toggle -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="editAutoAssignCheck">
                    <label class="form-check-label" for="editAutoAssignCheck">Automatically assign tags to the chat in which canned was used</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-update-shortcut">Update</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentEditId = null;
            let addShortcutTagify, addAssignTagify, editShortcutTagify, editAssignTagify;

            // Init Tagify
            function initTagify() {
                addShortcutTagify = new Tagify(document.querySelector('#shortcutModal .shortcut-input'), { delimiters: ',| ', maxTags: 10, dropdown: { enabled: 0 } });
                addAssignTagify = new Tagify(document.querySelector('#shortcutModal .assign-tags-input'), { whitelist: ['Support', 'Billing', 'Complaint', 'Order'], maxTags: 10, dropdown: { enabled: 0 } });
                
                editShortcutTagify = new Tagify(document.querySelector('#EditshortcutModal .shortcut-input'), { delimiters: ',| ', maxTags: 10, dropdown: { enabled: 0 } });
                editAssignTagify = new Tagify(document.querySelector('#EditshortcutModal .assign-tags-input'), { whitelist: ['Support', 'Billing', 'Complaint', 'Order'], maxTags: 10, dropdown: { enabled: 0 } });

                // Counter updates
                addAssignTagify.on('add remove', () => $('#shortcutModal label[for="assign-tags-input"] span').text(`(${addAssignTagify.value.length}/10 tags)`));
                editAssignTagify.on('add remove', () => $('#EditshortcutModal .edit-tag-count').text(`(${editAssignTagify.value.length}/10 tags)`));
            }
            initTagify();

            // Reset Add Modal
            $('#shortcutModal').on('hidden.bs.modal', function () {
                $(this).find('.response-text').val('');
                addShortcutTagify.removeAllTags();
                addAssignTagify.removeAllTags();
                $('#sharedCheck').prop('checked', false);
                $('#autoAssignCheck').prop('checked', false);
                $('#shortcutModal label[for="assign-tags-input"] span').text('(0/10 tags)');
            });

            // SAVE NEW
            $('#btn-save-shortcut').on('click', function () {
                const shortcuts = addShortcutTagify.value.map(t => t.value);
                const shortcutStr = shortcuts.length > 0 ? shortcuts[0] : ''; // Takes first tag as main shortcut

                if (!shortcutStr || !$('#shortcutModal .response-text').val().trim()) {
                    Swal.fire('Error', 'Shortcut and Response Text are required.', 'error');
                    return;
                }

                $.ajax({
                    url: '{{ route("settings.shortcut.store") }}',
                    type: 'POST',
                    data: {
                        shortcut: shortcutStr,
                        response_text: $('#shortcutModal .response-text').val(),
                        tags: addAssignTagify.value.map(t => t.value),
                        is_shared: $('#sharedCheck').is(':checked') ? 1 : 0,
                        auto_apply_tags: $('#autoAssignCheck').is(':checked') ? 1 : 0,
                    },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        Swal.fire('Success', res.message, 'success').then(() => location.reload());
                    },
                    error: function(err) {
                        Swal.fire('Error', err.responseJSON?.message || 'Validation Error', 'error');
                    }
                });
            });

            // OPEN EDIT
            $(document).on('click', '.editShortcut', function () {
                const listEl = $(this).closest('.shortcut-list');
                currentEditId = listEl.data('id');

                editShortcutTagify.removeAllTags();
                editAssignTagify.removeAllTags();

                // Handle shortcuts (comma separated string)
                const rawShortcut = listEl.data('shortcut');
                if(rawShortcut) editShortcutTagify.addTags(rawShortcut.split(','));

                // Handle tags (JSON array)
                const rawTags = listEl.data('tags');
                if(rawTags && rawTags.length > 0) editAssignTagify.addTags(rawTags);

                $('#EditshortcutModal .response-text').val(listEl.data('response'));
                $('#editSharedCheck').prop('checked', listEl.data('shared') === 'true');
                $('#editAutoAssignCheck').prop('checked', listEl.data('auto') === 'true');
                
                $('#EditshortcutModal').modal('show');
            });

            // UPDATE EXISTING
            const updateShortcutUrl = "{{ route('settings.shortcut.update', ':id') }}";

            $('#btn-update-shortcut').on('click', function () {
                const shortcuts = editShortcutTagify.value.map(t => t.value);
                const shortcutStr = shortcuts.length > 0 ? shortcuts[0] : '';

                if (!shortcutStr || !$('#EditshortcutModal .response-text').val().trim()) {
                    Swal.fire('Error', 'Shortcut and Response Text are required.', 'error');
                    return;
                }

                $.ajax({
                    url: updateShortcutUrl.replace(':id', currentEditId),
                    type: 'POST',
                    data: {
                        _method: 'PUT',
                        shortcut: shortcutStr,
                        response_text: $('#EditshortcutModal .response-text').val(),
                        tags: editAssignTagify.value.map(t => t.value),
                        is_shared: $('#editSharedCheck').is(':checked') ? 1 : 0,
                        auto_apply_tags: $('#editAutoAssignCheck').is(':checked') ? 1 : 0,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        Swal.fire('Success', res.message, 'success')
                            .then(() => location.reload());
                    },
                    error: function(err) {
                        Swal.fire(
                            'Error',
                            err.responseJSON?.message || 'Validation Error',
                            'error'
                        );
                    }
                });
            });

            // DELETE
            const deleteShortcutUrl = "{{ route('settings.shortcut.destroy', ':id') }}";

            $(document).on('click', '.deletShortcut', function () {
                const id = $(this).data('shortcutid');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--bs-primary)',
                    cancelButtonColor: 'var(--bs-danger)',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: deleteShortcutUrl.replace(':id', id),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },

                            success: function(res) {
                                Swal.fire(
                                    'Deleted!',
                                    res.message,
                                    'success'
                                ).then(() => location.reload());
                            },

                            error: function(err) {
                                Swal.fire(
                                    'Error',
                                    err.responseJSON?.message || 'Delete failed',
                                    'error'
                                );
                            }
                        });

                    }
                });
            });

            // Variable Insert
            function insertAtCursor(myField, myValue) {
                if (myField.selectionStart || myField.selectionStart === 0) {
                    var startPos = myField.selectionStart, endPos = myField.selectionEnd;
                    myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
                    myField.selectionStart = myField.selectionEnd = startPos + myValue.length;
                } else { myField.value += myValue; }
            }

            $(document).on('click', '.variable-item', function (e) {
                e.preventDefault();
                let value = $(this).data('value') + ' ';
                let textarea = $(this).closest('.modal').find('.response-text')[0];
                if (textarea) insertAtCursor(textarea, value);
            });

            // Search Filter (Client side)
            $('#shortcut-search-input').on('input', function() {
                let val = $(this).val().toLowerCase();
                $('.shortcut-list').each(function() {
                    let text = $(this).find('.hastag-short').text().toLowerCase() + ' ' + $(this).find('.shortcut-msg p').text().toLowerCase();
                    $(this).toggle(text.includes(val));
                });
            });
        });
    </script>
@endpush