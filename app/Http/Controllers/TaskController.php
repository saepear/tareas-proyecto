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
        $filterPriority = $request->query('priority');

        $filteredTasks = $tasks;

        if ($filterStatus && $filterStatus !== 'all') {
            $filteredTasks = $filteredTasks->where('status.name', $filterStatus);
        }

        if ($filterPriority) {
            $filteredTasks = $filteredTasks->where('priority.name', $filterPriority);
        }

        $stats = [
            'total' => $tasks->count(),
            'pending' => $tasks->filter(fn($t) => $t->status->name === 'pending')->count(),
            'in_progress' => $tasks->filter(fn($t) => $t->status->name === 'in-progress')->count(),
            'completed' => $tasks->filter(fn($t) => $t->status->name === 'completed')->count(),
        ];

        $combinedStats = [];
        foreach (['pending', 'in-progress', 'completed'] as $st) {
            foreach (['alta', 'media', 'baja'] as $pr) {
                $combinedStats[$st][$pr] = $tasks
                    ->filter(fn($t) => $t->status->name === $st && $t->priority->name === $pr)
                    ->count();
            }
        }

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

        $displayName = $user->display_name ?: ($user->first_name . ' ' . $user->last_name);
        $taskStates = TaskState::all();
        $editableStates = $taskStates->reject(fn($s) => $s->name === 'completed');
        $priorityTypes = PriorityType::all();

        $statusNameToLabel = [
            'pending' => 'Pendiente',
            'in-progress' => 'En Progreso',
            'completed' => 'Completada',
        ];

        if ($request->ajax()) {
            return view('partials.tasks-container', compact('filteredTasks', 'statusNameToLabel'));
        }

        return view('tasks', compact(
            'tasks', 'filteredTasks', 'stats', 'filterStatus', 'filterPriority',
            'statusLabels', 'currentPage', 'user', 'displayName',
            'taskStates', 'editableStates', 'priorityTypes', 'combinedStats', 'statusNameToLabel'
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

        $duplicate = Task::where('user_id', Auth::id())
            ->where('title', $request->title)
            ->where('description', $request->description)
            ->where('status_id', $request->status_id)
            ->where('priority_id', $request->priority_id)
            ->where('due_date', $request->due_date)
            ->exists();

        if ($duplicate) {
            return back()->with('error', 'Ya existe una tarea idéntica con la misma configuración, prioridad y fecha límite.');
        }

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

    public function toggleStatus(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $completedState = TaskState::where('name', 'completed')->first();
        $pendingState = TaskState::where('name', 'pending')->first();

        if ($task->status_id === $completedState->id) {
            $task->status_id = $pendingState->id;
            $task->completion_date = null;
        } else {
            $task->status_id = $completedState->id;
            $task->completion_date = now();
        }

        $task->updated_by = Auth::id();
        $task->save();

        $label = match ($task->status->name) {
            'pending' => 'Pendiente',
            'in-progress' => 'En Progreso',
            'completed' => 'Completada',
            default => $task->status->name,
        };

        return response()->json([
            'success' => true,
            'status' => $task->status->name,
            'status_label' => $label,
        ]);
    }
}
