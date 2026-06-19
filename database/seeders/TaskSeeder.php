<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskState;
use App\Models\PriorityType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@example.com',
        ]);

        $states = TaskState::pluck('id', 'name');
        $priorities = PriorityType::pluck('id', 'name');
        $completedId = $states['completed'];

        $titles = [
            'Corregir estilos neumórficos',
            'Optimizar queries de MongoDB',
            'Revisar logs con Laravel Pail',
            'Implementar autenticación con Laravel Breeze',
            'Diseñar migraciones para el módulo de ventas',
            'Refactorizar controlador de tareas',
            'Agregar validación al formulario de registro',
            'Configurar colas con Redis',
            'Escribir tests para el modelo User',
            'Actualizar dependencias de Composer',
            'Limpiar caché de rutas y config',
            'Documentar API con Swagger',
            'Corregir bug en el sidebar responsive',
            'Optimizar carga de imágenes con lazy loading',
            'Implementar búsqueda full-text con Laravel Scout',
            'Migrar servidor a PHP 8.4',
            'Configurar monitoreo con Laravel Pulse',
            'Agregar filtros avanzados al reporte diario',
            'Corregir error 500 en el endpoint de usuarios',
            'Rediseñar la página de inicio',
            'Configurar entorno de staging',
            'Implementar exportación a PDF',
            'Agregar notificaciones por correo',
            'Optimizar pipeline de CI/CD',
            'Revisar vulnerabilidades de seguridad',
            'Actualizar políticas de privacidad',
            'Configurar backups automáticos',
            'Agregar paginación a la tabla de logs',
            'Traducir interfaz a inglés y portugués',
            'Implementar modo oscuro en el panel',
        ];

        $tasks = [];

        for ($i = 0; $i < 30; $i++) {
            $pastDue = fake()->boolean(40);
            $statusId = $states->random();
            $priorityId = $priorities->random();

            $tasks[] = [
                'title' => fake()->randomElement($titles),
                'description' => fake()->boolean(70) ? fake()->sentence(fake()->numberBetween(4, 12)) : '',
                'status_id' => $statusId,
                'priority_id' => $priorityId,
                'due_date' => $pastDue
                    ? fake()->dateTimeBetween('-30 days', '-1 days')->format('Y-m-d')
                    : fake()->dateTimeBetween('now', '+14 days')->format('Y-m-d'),
                'completion_date' => $statusId === $completedId
                    ? fake()->dateTimeBetween('-30 days', 'now')
                    : null,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Task::insert($tasks);
    }
}
