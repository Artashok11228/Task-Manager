<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use League\CommonMark\Extension\TaskList\TaskListExtension;

class TasksController extends Controller
{
    public function getAll()
    {
        $all = Task::all()->toArray();
        return response()->json([$all], 200);
    }

    public function getOne($id)
    {
        $query = Task::query()->Find($id);
        if ($query) {
            return response()->json($query);
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
            'completed' => 'nullable|boolean',
        ]);

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
        $validate = $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'completed' => 'nullable|boolean',
        ]);
        if ($validate) {
            $query = Task::query()->FindOrFail($id)->update($validate);
            if ($query) {
                return response()->json([
                    'message' => 'Task updated',
                ]);
            }
            return response()->json([
                'message' => 'Task not updated , something went wrong',
            ], 500);
        }

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
