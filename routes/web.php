<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Tasks are the primary resource. The reorder endpoint sits alongside the
| standard resourceful routes as a dedicated PATCH action, keeping the
| controller slim and the intent clear.
|
*/

// Redirect root to the task list.
Route::redirect('/', '/tasks');

Route::resource('tasks', TaskController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

Route::patch('reorder', [TaskController::class, 'reorder'])
    ->name('reorder');


Route::resource('projects', ProjectController::class)
    ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
