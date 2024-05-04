<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\TaskRequest;
use App\Services\TaskService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function create(TaskRequest $request, TaskService $taskService): JsonResponse
    {
        $dateArray = $taskService->setDueDate($request);
        $task = $taskService->createTask($request, $dateArray['startDate'], $dateArray['dueDate']);

        return response()->json(['message' => 'Task created successfully!', 'task' => $task], 201);
    }

    /**
     * @throws AuthenticationException
     */
    public function setTask(Request $request, TaskService $taskService): JsonResponse
    {
        $taskService->setTask($request);

        return response()->json(['message' => 'Task successfully set']);
    }

    /**
     * @throws Exception
     */
    public function getAll(TaskService $taskService): JsonResponse
    {
        $tasks = $taskService->getAll();

        return response()->json($tasks);
    }

    /**
     * @throws Exception
     */
    public function getById(Request $request, TaskService $taskService): JsonResponse
    {
        $task = $taskService->getById($request);

        return response()->json($task);
    }

    /**
     * @throws Exception
     */
    public function update(TaskRequest $request, TaskService $taskService): JsonResponse
    {
        $dateArray = $taskService->setDueDate($request);
        $task = $taskService->update($request, $dateArray['dueDate']);

        return response()->json(['message' => 'Task updated successfully!', 'task' => $task]);
    }

    /**
     * @throws Exception
     */
    public function delete(Request $request, TaskService $taskService): JsonResponse
    {
        $taskService->delete($request);

        return response()->json(['message' => 'Task deleted successfully!']);
    }
}
