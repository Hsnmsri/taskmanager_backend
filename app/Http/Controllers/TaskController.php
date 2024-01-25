<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\ResponseBodyBuilder;

class TaskController extends Controller
{
    public function create(Request $request)
    {
        try {
            $task = new Task();
            $task->user_id = $request->user_id;
            $task->title = trim($request->title);
            $task->description = trim($request->description);
            $task->require_time = $request->require_time;
            $task->category_id = $request->category_id;
            $task->status = 1;
            if (!$task->save()) {
                return ResponseBodyBuilder::buildFailureResponse(5);
            }
            return ResponseBodyBuilder::buildSuccessResponse(null);
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $task = Task::find($request->task_id);
            $task->title = trim($request->title);
            $task->description = trim($request->description);
            $task->require_time = $request->require_time;
            $task->category_id = $request->category_id;
            if (!$task->update()) {
                return ResponseBodyBuilder::buildFailureResponse(5);
            }
            return ResponseBodyBuilder::buildSuccessResponse(null);
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            Task::find($request->task_id)->delete();
            return ResponseBodyBuilder::buildSuccessResponse(null);
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    public function taskDone(Request $request)
    {
        try {
            $task = Task::find($request->task_id);
            $task->status = 0;
            if (!$task->update()) {
                return ResponseBodyBuilder::buildFailureResponse(5);
            }
            return ResponseBodyBuilder::buildSuccessResponse(null);
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    public function taskRestore(Request $request)
    {
        try {
            $task = Task::find($request->task_id);
            $task->status = 1;
            if (!$task->update()) {
                return ResponseBodyBuilder::buildFailureResponse(5);
            }
            return ResponseBodyBuilder::buildSuccessResponse(null);
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }

    public function getList(Request $request)
    {
        try {
            $tasks = User::find($request->user_id)->tasks;
            return ResponseBodyBuilder::buildSuccessResponse(null, $tasks);
        } catch (Exception $error) {
            return ResponseBodyBuilder::buildFailureResponse(0, $error->getMessage());
        }
    }
}
