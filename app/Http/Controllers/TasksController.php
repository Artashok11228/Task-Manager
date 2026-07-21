<?php

namespace App\Http\Controllers;

use App\ApiResponse\ApiResponse;
use App\Http\Requests\editTask;
use App\Http\Requests\storeTask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\CommonMark\Extension\TaskList\TaskListExtension;

class TasksController extends Controller
{
    public function getAll(Request $request)
    {
        $tasks = $request->user()->tasks()->with('status')->get();
        return ApiResponse::builder()
            ->withData($tasks)
            ->withStatus(200)
            ->build()
            ->response();
    }

    public function getOne($id, Request $request)
    {
        $task = $request->user()->tasks()->with('status')->find($id);
        if ($task) {
            return ApiResponse::builder()
                ->withData($task)
                ->withStatus(200)
                ->build()
                ->response();
        }
        return ApiResponse::builder()
            ->withMessage("Task not found")
            ->withStatus(404)
            ->build()
            ->response();
    }

    public function addTask(storeTask $request)
    {
        $validate = $request->validated();
        $validate['user_id'] = $request->user()->id;
        if ($validate) {
            $query = Task::query()->create($validate);
            $result = Task::find($query->id);
            if ($query) {
                return ApiResponse::builder()
                    ->withMessage('task created succesfully')
                    ->withData($result)
                    ->withStatus(200)
                    ->build()
                    ->response();
            }
        }
        return ApiResponse::builder()
            ->withMessage("cant create task. something went wrong")
            ->withStatus(500)
            ->build()
            ->response();
    }

    public function editTask(storeTask $request, $id)
    {
        $task = $request->user()->tasks()->findOrFail($id);
        $validate = $request->validated();

        $query = $task->update($validate);
        if ($query) {
            $result = Task::query()->findOrFail($id) ?? 'data not avalaible';

            return ApiResponse::builder()
                ->withMessage('task updated successfully')
                ->withData($task)
                ->withStatus(200)
                ->build()
                ->response();
        }
        return ApiResponse::builder()
            ->withMessage('cant do that. something went wrong')
            ->withData($task)
            ->withStatus(500)
            ->build()
            ->response();


    }

    public function deleteTask($id)
    {
        $query = Task::query()->Find($id);
        if ($query) {
            $query->delete();
            return APiResponse::builder()
                ->withMessage('task deleted successfully')
                ->withStatus(200)
                ->build()
                ->response();
        }
        return ApiResponse::builder()
            ->withMessage("task not found")
            ->withStatus(404)
            ->build()
            ->response();

    }
}
