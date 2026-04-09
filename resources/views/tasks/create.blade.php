@extends('layouts.app')

@section('title', 'New Task')

@section('content')

<div class="page-header">
    <h1 class="page-title">New Task</h1>
</div>

<div class="card" style="padding: 1.75rem 2rem; max-width: 520px;">
    <form action="{{ route('tasks.store') }}" method="POST" class="form">
        @csrf

        {{-- Task name --}}
        <div class="field">
            <label for="name">Task Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="e.g. Write release notes"
                autofocus
                required
            >
            @error('name')
                <span class="field__error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Project (optional) --}}
        <div class="field">
            <label for="project_id">Project <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
            <select id="project_id" name="project_id">
                <option value="">— No project —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}"
                        {{ old('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @error('project_id')
                <span class="field__error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Create Task</button>
            <a href="{{ route('tasks.index') }}" class="btn btn--ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
