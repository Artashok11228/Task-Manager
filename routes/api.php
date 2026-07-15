<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {


    Route::get('/tasks', [TasksController::class, 'getAll'])->name('Tasks');
    Route::get('/tasks/{id}', [TasksController::class, 'getOne'])->name('Get One Task');
    Route::post('/tasks', [TasksController::class, 'addTask'])->name('Add Task');
    Route::put('/tasks/{id}', [TasksController::class, 'editTask'])->name('Update Task');
    Route::delete('/tasks/{id}', [TasksController::class, 'deleteTask'])->name('Delete Task');

    Route::get('/roles', [RolesController::class, 'getAll'])->name('Roles');
    Route::post('/roles', [RolesController::class, 'addRole'])->name('Add Role');

});
