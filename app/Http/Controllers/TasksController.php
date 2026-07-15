<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use League\CommonMark\Extension\TaskList\TaskListExtension;

class TasksController extends Controller
{
    public function getAll(Request $request)
    {
        $tasks = $request->user()->tasks()->with('status')->get();
        return response()->json([$tasks], 200);
    }

    public function getOne($id, Request $request)
    {
        $task = $request->user()->tasks()->with('status')->find($id);
        if ($task) {
            return response()->json($task);
        }
        return response()->json([
            'message' => 'Task not found'
        ], 404);
    }

    public function addTask(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'status' => 'nullable|string',
        ]);
        $validate['user_id'] = $request->user()->id;


        if ($validate) {
            $query = Task::query()->create($validate);
            $result = Task::find($query->id);
            if ($query) {
                return response()->json([
                    'message' => 'Task created',
                    'data' => $result
                ]);
            }
        }
        return response()->json([
            'message' => 'Task not created , something went wrong',
        ], 500);

    }

    public function editTask(Request $request, $id)
    {
        $task = $request->user()->tasks()->findOrFail($id);

        $validate = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'status_id' => 'nullable|string',
        ]);
        $query = $task->update($validate);
        if ($query) {
            return response()->json([
                'message' => 'Task updated',
                'data' => Task::query()->findOrFail($id) ?? 'unavalaible data'
            ]);

        }
        return response()->json(['message' => 'Task not updated , something went wrong'], 500);


    }


    public function deleteTask($id)
    {
        $query = Task::query()->Find($id);
        if ($query) {
            $query->delete();
            return response()->json([
                'message' => 'Task deleted',
            ]);
        }
        return response()->json([
            'message' => 'Task not found',
        ]);
    }
}
