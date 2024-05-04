<?php

namespace App\Services;

use App\Http\Requests\Tasks\TaskRequest;
use App\Models\Task;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class TaskService
{
    /**
     * @throws Exception
     */
    public function createTask(TaskRequest $request, $startDate, $dueDate)
    {
        try {
            return Task::create([
                'title' => $request['title'],
                'description' => $request['description'],
                'status' => $request['status'],
                'priority' => $request['priority'],
                'start_date' => $startDate,
                'due_date' => $dueDate,
                'user_id' => $request['user_id'],
            ]);
        } catch (\Exception) {
            throw new Exception('Could not create task', 500);
        }
    }

    /**
     * @throws Exception
     */
    public function setDueDate(TaskRequest $request): array
    {
        $startDate = $request['start_date'] ? new DateTime($request['start_date']) : new DateTime();

        $dueDate = null;
        if (!empty($request['due_duration'])) {
            $durationParts = explode(' ', $request['due_duration']);

            $days = (int)$durationParts[0];
            $hours = (int)$durationParts[2];
            $minutes = (int)$durationParts[4];
            $intervalSpec = "P{$days}DT{$hours}H{$minutes}M";

            $interval = new DateInterval($intervalSpec);
            $dueDate = clone $startDate;
            $dueDate->add($interval);
        }

        return ['startDate' => $startDate, 'dueDate' => $dueDate];
    }

    /**
     * @throws AuthenticationException
     * @throws Exception
     */
    public function setTask(Request $request): void
    {
        try {
            if ($request->user()->role !== 'admin') {
                throw new AuthenticationException('You do not have the necessary permissions to view this section.');
            }

            $task = Task::where('id', $request['task_id'])->first();
            $task->user_id = $request['user_id'];
            $task->save();

        } catch (Exception $e) {
            throw new Exception('Could not set task. ' . $e->getMessage(), 500);
        }
    }

    /**
     * @throws Exception
     */
    public function getAll()
    {
        try {
            Task::where('due_date', '<', now())
                ->where('status', '!=', 'expired')
                ->update(['status' => 'expired']);

            return Task::orderBy('due_date', 'asc')->paginate(10);
        } catch (Exception) {
            throw new Exception('Could not get tasks', 500);
        }
    }

    /**
     * @throws Exception
     */
    public function getById(Request $request)
    {
        try {
            $task = Task::where("id", $request->id)->first();

            if ($task) {
                $currentDate = new DateTime();
                $dueDate = new DateTime($task->due_date);

                if ($dueDate < $currentDate) {
                    $task->status = 'expired';
                    $task->save();
                }

                return $task;
            }

            throw new Exception('No such id in tasks table', 400);
        } catch (Exception $e) {
            throw new Exception('Could not get task. ' . $e->getMessage(), 500);
        }
    }

    /**
     * @throws Exception
     */
    public function update(TaskRequest $request, $dueDate)
    {
        try {
            $task = Task::where("id", $request['task_id'])->first();

            $task->update([
                'title' => $request['title'],
                'description' => $request['description'],
                'status' => $request['status'],
                'priority' => $request['priority'],
                'start_date' => $request['start_date'],
                'due_date' => $dueDate,
            ]);

            return $task;
        } catch (Exception) {
            throw new Exception('Could not update task', 500);
        }
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request): void
    {
        try {
            $task = Task::where("id", $request->id)->first();

            if ($task) {
                $task->delete();
                return;
            }

            throw new Exception('No such id in tasks table', 400);
        } catch (Exception $e) {
            throw new Exception('Could not get task. ' . $e->getMessage(), 500);
        }
    }
}
