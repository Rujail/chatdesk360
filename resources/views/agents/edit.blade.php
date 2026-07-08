@extends('layouts.app')

@section('title', 'Edit Agent')

@section('content')
<div class="body-wrapper mb-0 pg-agent-edit">
    <div class="container-fluid">
        <div class="card card-body py-3">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-4 mb-sm-0 card-title">Profile</h4>
                        <div class="d-flex align-items-center ms-auto">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item d-flex align-items-center">
                                        <a class="text-muted text-decoration-none d-flex" href="/">
                                            <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('agents.index') }}">
                                            <span class="badge fw-medium fs-2 bg-primary-subtle text-primary">Agents</span>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item" aria-current="page">
                                        <span class="fw-medium fs-2"> Profile Edit </span>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget-content searchable-container list">
            <div class="card card-body">
                <form action="{{ route('agents.update', $agent->id) }}" method="POST" class="profileEditForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-avatar-details">
                                <div class="circle upload-button">
                                    <div class="profile-initials rounded-circle d-flex align-items-center justify-content-center text-white fs-1 bg-primary" style="width:100px; height:100px;">
                                        {{ strtoupper(substr($agent->name, 0, 1)) }}
                                    </div>
                                    <div class="status">
                                        <span class="badge rounded-pill bg-success"> </span>
                                    </div>
                                </div>
                                <div class="profile-name">
                                    <span class="badge text-bg-dark">{{ ucfirst($agent->role) }}</span>
                                    <h3>{{ $agent->name }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="title mt-4">Details</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="name" class="form-control" value="{{ $agent->name }}" />
                            </div>
                            <div class="mb-3">
                                <div class="form-group chatbox">
                                    <label for="Chat-limit" class="form-label">Chat limit</label>
                                    <div class="quantity-box">
                                        <input class="quantity form-control" type="number" id="Chat-limit" name="concurrent_chat_limit" min="1" value="{{ $agent->concurrent_chat_limit }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $agent->email }}" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Groups</label>
                                <input type="text" name="groups" class="form-control" value="{{ $agent->groups }}" />
                            </div>
                        </div>
                    </div>

                    <h4 class="title">Agent status after logging in</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check py-1">
                                <input type="radio" id="Acceptchat" name="status" class="form-check-input" value="accepting_chats" {{ $agent->status == 'accepting_chats' ? 'checked' : '' }} />
                                <label class="form-check-label" for="Acceptchat">Accept chats</label>
                            </div>
                            <div class="form-check py-1">
                                <input type="radio" id="Acceptchat1" name="status" class="form-check-input" value="not_accepting_chats" {{ $agent->status == 'not_accepting_chats' ? 'checked' : '' }} />
                                <label class="form-check-label" for="Acceptchat1">Don't accept chats</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-primary" type="submit">Save</button>
                                <a href="{{ route('agents.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection