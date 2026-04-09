@extends('layouts.app')

@section('title', 'Projects')

@section('content')

<div class="page-header">
    <h1 class="page-title">Projects</h1>
    <a href="{{ route('projects.create') }}" class="btn btn--primary">+ New Project</a>
</div>

@if ($projects->isEmpty())
    <div class="empty">
        <div class="empty__icon">📁</div>
        <p class="empty__text">No projects yet. Create one to group your tasks.</p>
        <a href="{{ route('projects.create') }}" class="btn btn--primary">+ New Project</a>
    </div>
@else
    <ul class="task-list">
        @foreach ($projects as $project)
            <li class="task-item" style="cursor:default;">
                <span class="task-item__name">
                    <strong>{{ $project->name }}</strong>
                    @if ($project->description)
                        <br>
                        <span style="font-size:.85rem;color:var(--text-muted);font-weight:400;">
                            {{ $project->description }}
                        </span>
                    @endif
                </span>

                <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
                   class="badge">
                    {{ $project->tasks_count }}
                    {{ Str::plural('task', $project->tasks_count) }}
                </a>

                <span class="task-item__actions">
                    <a href="{{ route('projects.edit', $project) }}"
                       class="btn btn--ghost btn--sm">Edit</a>

                    <form action="{{ route('projects.destroy', $project) }}"
                          method="POST"
                          onsubmit="return confirm('Delete project and ALL its tasks?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn--danger btn--sm">Delete</button>
                    </form>
                </span>
            </li>
        @endforeach
    </ul>
@endif

@endsection
