@extends('layouts.app')

@section('title', 'New Project')

@section('content')

<div class="page-header">
    <h1 class="page-title">New Project</h1>
</div>

<div class="card" style="padding: 1.75rem 2rem; max-width: 520px;">
    <form action="{{ route('projects.store') }}" method="POST" class="form">
        @csrf

        <div class="field">
            <label for="name">Project Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="e.g. Website Redesign"
                autofocus
                required
            >
            @error('name')
                <span class="field__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="field">
            <label for="description">Description <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
            <textarea id="description" name="description" placeholder="What is this project about?">{{ old('description') }}</textarea>
            @error('description')
                <span class="field__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Create Project</button>
            <a href="{{ route('projects.index') }}" class="btn btn--ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
