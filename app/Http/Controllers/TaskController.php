<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\ReorderTasksRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    
    public function index(Request $request): View
    {
        $selectedProjectId = $request->integer('project_id') ?: null;

        $tasks = Task::with('project')
            ->forProject($selectedProjectId)
            ->orderBy('priority')
            ->get();

        $projects = Project::orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'projects', 'selectedProjectId'));
    }

    /**
     * Show the form for creating a new task.
     */
    public function create(): View
    {
        $projects = Project::orderBy('name')->get();

        return view('tasks.create', compact('projects'));
    }

    /**
     * Store a new task and place it at the end of the priority list.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $projectId = $request->input('project_id') ?: null;

        // Place the new task at the bottom of the current project's list.
        $lowestPriority = Task::forProject($projectId)->max('priority') ?? 0;

        Task::create([
            'name'       => $request->input('name'),
            'project_id' => $projectId,
            'priority'   => $lowestPriority + 1,
        ]);

        return redirect()
            ->route('tasks.index', array_filter(['project_id' => $projectId]))
            ->with('success', 'Task created successfully.');
    }

    /**
     * Show the form for editing an existing task.
     */
    public function edit(Task $task): View
    {
        $projects = Project::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    /**
     * Update an existing task's name and/or project assignment.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $newProjectId = $request->input('project_id') ?: null;
        $projectChanged = $task->project_id !== $newProjectId;

        if ($projectChanged) {
            // Move task to the bottom of the new project's list.
            $lowestPriority = Task::forProject($newProjectId)->max('priority') ?? 0;
            $task->priority = $lowestPriority + 1;
        }

        $task->name       = $request->input('name');
        $task->project_id = $newProjectId;
        $task->save();

       
        if ($projectChanged) {
            Task::resequencePriorities($task->getOriginal('project_id'));
        }

        return redirect()
            ->route('tasks.index', array_filter(['project_id' => $newProjectId]))
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Delete a task and re-sequence the remaining tasks in its project.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;

        $task->delete();

        Task::resequencePriorities($projectId);

        return redirect()
            ->route('tasks.index', array_filter(['project_id' => $projectId]))
            ->with('success', 'Task deleted successfully.');
    }

   
    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
       
        $orderedIds = $request->input('ordered_ids');
  
        foreach ($orderedIds as $priority => $id) {
            Task::where('id', $id)->update(['priority' => $priority + 1]);
        }

        return response()->json(['message' => 'Tasks reordered successfully.']);
    }
}
