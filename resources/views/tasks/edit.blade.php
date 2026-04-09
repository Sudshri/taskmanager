@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')

<div class="page-header">
    <h1 class="page-title">Edit Task</h1>
</div>

<div class="card" style="padding: 1.75rem 2rem; max-width: 520px;">
    <form action="{{ route('tasks.update', $task) }}" method="POST" class="form">
        @csrf
        @method('PUT')

        {{-- Task name --}}
        <div class="field">
            <label for="name">Task Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $task->name) }}"
                autofocus
                required
            >
            @error('name')
                <span class="field__error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Project --}}
        <div class="field">
            <label for="project_id">Project <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
            <select id="project_id" name="project_id">
                <option value="">— No project —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}"
                        {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @error('project_id')
                <span class="field__error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Current priority (read-only info) --}}
        <p style="font-size:.85rem; color:var(--text-muted);">
            Current priority: <strong>#{{ $task->priority }}</strong>
            — drag to reorder on the task list.
        </p>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Save Changes</button>
            <a href="{{ route('tasks.index', array_filter(['project_id' => $task->project_id])) }}"
               class="btn btn--ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
