<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Models\Task;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:new,in progress,completed',
            'priority' => 'required|integer|min:1|max:3',
            'start_date' => 'nullable|date_format:Y-m-d H:i:s',
            'due_duration' => 'nullable|regex:/^\d+\s+days\s+\d+\s+hours\s+\d+\s+minutes$/',
            'user_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();

        $startDate = $validated['start_date'] ? new DateTime($validated['start_date']) : new DateTime();
        $dueDate = null;

        if (!empty($validated['due_duration'])) {
            $durationParts = explode(' ', $validated['due_duration']);
            if (count($durationParts) == 6) {
                $days = (int)$durationParts[0];
                $hours = (int)$durationParts[2];
                $minutes = (int)$durationParts[4];
                $intervalSpec = "P{$days}DT{$hours}H{$minutes}M";
                try {
                    $interval = new DateInterval($intervalSpec);
                    $dueDate = clone $startDate;
                    $dueDate->add($interval);
                } catch (Exception $e) {
                    return response()->json(['error' => 'Invalid duration format'], 400);
                }
            } else {
                return response()->json(['error' => 'Duration format should be "X days Y hours Z minutes"'], 400);
            }
        }

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'],
            'due_date' => $dueDate,
            'user_id' => $validated['user_id'],
        ]);

        return response()->json(['message' => 'Task created successfully!', 'task' => $task], 201);
    }

    public function setTask(Request $request)
    {
        if ($request->user()->role === 'admin') {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|exists:tasks,id',
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }
            $validated = $validator->validated();

            $task = Task::where('id', $validated['task_id'])->first();
            $task->user_id = $validated['user_id'];
            $task->save();

            return response()->json(['message' => 'Task successfully set']);
        }


        return response()->json(['error' => 'Access denied. You do not have the necessary permissions to view this section.'], 401);
    }

    public function getAll(Request $request)
    {
        // Automatically update the status of tasks that are overdue
        Task::where('due_date', '<', now())
            ->where('status', '!=', 'expired')
            ->update(['status' => 'expired']);

        // Fetch tasks with pagination to improve load times and memory usage
        $tasks = Task::orderBy('due_date', 'asc')
            ->paginate(10);

        return response()->json($tasks);
    }

    public function getById(Request $request)
    {
        $task = Task::where("id", $request->id)->first();

        if ($task) {
            $currentDate = new DateTime(); // Current date and time
            $dueDate = new DateTime($task->due_date);

            // Check if the current date is greater than the due date
            if ($dueDate < $currentDate) {
                $task->status = 'expired';
                $task->save(); // Save the updated status to the database
            }

            return response()->json($task);
        }

        return response()->json(['error' => 'No such id in tasks table'], 400);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'nullable|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|string|in:new,in progress,completed',
            'priority' => 'required|integer|min:1|max:3',
            'start_date' => 'nullable|date_format:Y-m-d H:i:s',
            'due_duration' => 'nullable|regex:/^\d+\s+days\s+\d+\s+hours\s+\d+\s+minutes$/',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $validated = $validator->validated();
        $startDate = $validated['start_date'] ? new DateTime($validated['start_date']) : new DateTime();
        $dueDate = null;

        if (!empty($validated['due_duration'])) {
            $durationParts = explode(' ', $validated['due_duration']);
            if (count($durationParts) == 6) {
                $days = (int)$durationParts[0];
                $hours = (int)$durationParts[2];
                $minutes = (int)$durationParts[4];
                $intervalSpec = "P{$days}DT{$hours}H{$minutes}M";
                try {
                    $interval = new DateInterval($intervalSpec);
                    $dueDate = clone $startDate;
                    $dueDate->add($interval);
                } catch (Exception $e) {
                    return response()->json(['error' => 'Invalid duration format'], 400);
                }
            } else {
                return response()->json(['error' => 'Duration format should be "X days Y hours Z minutes"'], 400);
            }
        }

        $task = Task::where("id", $validated['task_id'])->first();

        $task->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'],
            'due_date' => $dueDate,
        ]);


        return response()->json(['message' => 'Task updated successfully!', 'task' => $task]);
    }

    public function delete(Request $request)
    {
        $task = Task::where("id", $request->id)->first();

        if($task){
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully!']);
        }

        return response()->json(['error' => 'No such id in tasks table'], 400);
    }
}
