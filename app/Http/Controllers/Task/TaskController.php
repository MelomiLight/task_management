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

        return response()->json(['message' => 'Task created successfully!'], 201);

    }
}
