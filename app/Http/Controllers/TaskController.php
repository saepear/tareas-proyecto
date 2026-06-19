<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskState;
use App\Models\PriorityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $tasks = Task::with(['status', 'priority'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $filterStatus = $request->query('status');

        $filteredTasks = $filterStatus && $filterStatus !== 'all'
            ? $tasks->where('status.name', $filterStatus)
            : $tasks;

        $stats = [
            'total' => $tasks->count(),
            'pending' => $tasks->filter(fn($t) => $t->status->name === 'pending')->count(),
            'in_progress' => $tasks->filter(fn($t) => $t->status->name === 'in-progress')->count(),
            'completed' => $tasks->filter(fn($t) => $t->status->name === 'completed')->count(),
        ];

        $statusLabels = TaskState::pluck('name', 'id')->map(function ($name) {
            return match ($name) {
                'pending' => 'Pendiente',
                'in-progress' => 'En Progreso',
                'completed' => 'Completada',
                default => $name,
            };
        });

        $pageInfo = [
            '' => ['title' => 'Todas las Tareas', 'subtitle' => 'Tienes ' . $stats['total'] . ' tareas en total.'],
            'all' => ['title' => 'Todas las Tareas', 'subtitle' => 'Tienes ' . $stats['total'] . ' tareas en total.'],
            'pending' => ['title' => 'Tareas Pendientes', 'subtitle' => 'Tienes ' . $filteredTasks->count() . ' tareas pendientes.'],
            'in-progress' => ['title' => 'Tareas en Progreso', 'subtitle' => 'Tienes ' . $filteredTasks->count() . ' tareas en progreso.'],
            'completed' => ['title' => 'Tareas Completadas', 'subtitle' => 'Tienes ' . $filteredTasks->count() . ' tareas completadas.'],
        ];

        $currentPage = $pageInfo[$filterStatus] ?? $pageInfo['all'];

        $displayName = $user->display_name ?? ($user->first_name . ' ' . $user->last_name);
        $taskStates = TaskState::all();
        $priorityTypes = PriorityType::all();

        return view('tasks', compact(
            'tasks', 'filteredTasks', 'stats', 'filterStatus',
            'statusLabels', 'currentPage', 'user', 'displayName',
            'taskStates', 'priorityTypes'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status_id' => ['required', 'exists:task_states,id'],
            'priority_id' => ['required', 'exists:priority_types,id'],
            'due_date' => ['required', 'date'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['created_by'] = Auth::id();

        Task::create($validated);

        return redirect()->route('tasks')->with('success', 'Tarea creada exitosamente.');
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status_id' => ['required', 'exists:task_states,id'],
            'priority_id' => ['required', 'exists:priority_types,id'],
            'due_date' => ['required', 'date'],
            'completion_date' => ['nullable', 'date'],
        ]);

        $validated['updated_by'] = Auth::id();

        if (isset($validated['status_id'])) {
            $completedState = TaskState::where('name', 'completed')->first();
            if ($completedState && (int)$validated['status_id'] === $completedState->id) {
                $validated['completion_date'] = now();
            } else {
                $validated['completion_date'] = null;
            }
        }

        $task->update($validated);

        return redirect()->route('tasks')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->deleted_by = Auth::id();
        $task->save();
        $task->delete();

        return redirect()->route('tasks')->with('success', 'Tarea eliminada exitosamente.');
    }
}
