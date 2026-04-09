@extends('layouts.app')

@section('title', 'Tasks')

@push('styles')
<style>
    /* ── Project filter bar ─────────────────────────────────────────── */
    .filter-bar {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-bar__label {
        font-size: .85rem;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .filter-pill {
        padding: .35rem .9rem;
        border-radius: 20px;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
        background: var(--surface);
        transition: background .15s, color .15s, border-color .15s;
        white-space: nowrap;
    }
    .filter-pill:hover { background: var(--bg); color: var(--text); }
    .filter-pill.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }

    /* ── Drag-and-drop list ─────────────────────────────────────────── */
    .task-list { list-style: none; display: flex; flex-direction: column; gap: .5rem; }

    .task-item {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .85rem 1rem;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        transition: box-shadow .2s, opacity .2s, transform .15s;
        cursor: grab;
        user-select: none;
    }
    .task-item:active { cursor: grabbing; }

    /* Visual feedback while dragging */
    .task-item.dragging {
        opacity: .45;
        box-shadow: none;
    }

    /* Drop-zone indicator */
    .task-item.drag-over {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37,99,235,.15);
    }

    .task-item__handle {
        color: var(--border);
        font-size: 1.1rem;
        flex-shrink: 0;
        cursor: grab;
        line-height: 1;
    }

    .task-item__name {
        flex: 1;
        font-weight: 500;
        font-size: .95rem;
        word-break: break-word;
    }

    .task-item__project {
        font-size: .78rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .task-item__actions {
        display: flex;
        gap: .4rem;
        flex-shrink: 0;
    }

    /* ── Save order button (shown after reorder) ────────────────────── */
    .save-order-bar {
        display: none;         /* shown via JS when order changes */
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .75rem 1rem;
        background: var(--badge-bg);
        border: 1.5px solid #C7D2FE;
        border-radius: var(--radius);
        margin-bottom: 1rem;
        font-size: .875rem;
        font-weight: 500;
        color: var(--badge-txt);
    }
    .save-order-bar.visible { display: flex; }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">Tasks</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn--primary">
        + New Task
    </a>
</div>

{{-- Project filter pills --}}
@if ($projects->isNotEmpty())
    <div class="filter-bar">
        <span class="filter-bar__label">Filter:</span>

        <a href="{{ route('tasks.index') }}"
           class="filter-pill {{ is_null($selectedProjectId) ? 'active' : '' }}">
            All Tasks
        </a>

        @foreach ($projects as $project)
            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}"
               class="filter-pill {{ $selectedProjectId === $project->id ? 'active' : '' }}">
                {{ $project->name }}
            </a>
        @endforeach
    </div>
@endif

{{-- Unsaved-order notification bar --}}
<div class="save-order-bar" id="saveOrderBar">
    <span>⇅ Order changed — save to persist priorities.</span>
    <button class="btn btn--primary btn--sm" id="saveOrderBtn">Save Order</button>
</div>

{{-- Task list --}}
@if ($tasks->isEmpty())
    <div class="empty">
        <div class="empty__icon">📋</div>
        <p class="empty__text">
            @if ($selectedProjectId)
                No tasks in this project yet.
            @else
                No tasks yet. Create your first one!
            @endif
        </p>
        <a href="{{ route('tasks.create') }}" class="btn btn--primary">+ New Task</a>
    </div>
@else
    <ul class="task-list" id="taskList">
        @foreach ($tasks as $task)
            <li class="task-item"
                draggable="true"
                data-id="{{ $task->id }}">

                {{-- Drag handle --}}
                <span class="task-item__handle" title="Drag to reorder">⠿</span>

                {{-- Priority badge --}}
                <span class="priority">{{ $task->priority }}</span>

                {{-- Task name + optional project label --}}
                <span class="task-item__name">
                    {{ $task->name }}
                    @if (is_null($selectedProjectId) && $task->project)
                        <br>
                        <span class="task-item__project">{{ $task->project->name }}</span>
                    @endif
                </span>

                {{-- Edit / Delete --}}
                <span class="task-item__actions">
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="btn btn--ghost btn--sm">Edit</a>

                    <form action="{{ route('tasks.destroy', $task) }}"
                          method="POST"
                          onsubmit="return confirm('Delete this task?')">
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

@push('scripts')
<script>

(function () {
    const list        = document.getElementById('taskList');
    const saveBar     = document.getElementById('saveOrderBar');
    const saveBtn     = document.getElementById('saveOrderBtn');
    const reorderUrl  = "{{ route('reorder') }}";
    const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;

    if (!list) return; 

    let dragged      = null;   
    let orderChanged = false;

    // ── Drag events ──────────────────────────────────────────────────────

    list.addEventListener('dragstart', (e) => {
        dragged = e.target.closest('.task-item');
        if (!dragged) return;
        dragged.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    });

    list.addEventListener('dragend', () => {
        if (dragged) dragged.classList.remove('dragging');
        document.querySelectorAll('.task-item').forEach(el => el.classList.remove('drag-over'));
        dragged = null;
    });

    list.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';

        const target = e.target.closest('.task-item');
        if (!target || target === dragged) return;

        // Determine whether to insert before or after the target.
        const rect = target.getBoundingClientRect();
        const midY = rect.top + rect.height / 2;

        document.querySelectorAll('.task-item').forEach(el => el.classList.remove('drag-over'));
        target.classList.add('drag-over');

        if (e.clientY < midY) {
            list.insertBefore(dragged, target);
        } else {
            list.insertBefore(dragged, target.nextSibling);
        }

        refreshPriorityBadges();
        orderChanged = true;
        saveBar.classList.add('visible');
    });

    list.addEventListener('drop', (e) => {
        e.preventDefault();
    });

    // ── Save order ───────────────────────────────────────────────────────

    saveBtn.addEventListener('click', async () => {
        const orderedIds = [...list.querySelectorAll('.task-item')].map(el =>
            parseInt(el.dataset.id, 10)
        );

        saveBtn.disabled    = true;
        saveBtn.textContent = 'Saving…';

        try {
            const response = await fetch(reorderUrl, {
                method:  'PATCH',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     csrfToken,
                    'Accept':           'application/json',
                },
                body: JSON.stringify({ ordered_ids: orderedIds }),
            });

            if (!response.ok) throw new Error('Server error');

            orderChanged = false;
            saveBar.classList.remove('visible');
        } catch (err) {
            alert('Could not save the new order. Please try again.');
        } finally {
            saveBtn.disabled    = false;
            saveBtn.textContent = 'Save Order';
        }
    });

  
    function refreshPriorityBadges() {
        list.querySelectorAll('.task-item').forEach((el, index) => {
            const badge = el.querySelector('.priority');
            if (badge) badge.textContent = index + 1;
        });
    }
})();
</script>
@endpush
