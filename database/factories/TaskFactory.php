<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskState;
use App\Models\PriorityType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
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

        $pastDue = fake()->boolean(40);

        return [
            'title' => fake()->randomElement($titles),
            'description' => fake()->boolean(70) ? fake()->sentence(fake()->numberBetween(4, 12)) : '',
            'status_id' => TaskState::inRandomOrder()->first()->id,
            'priority_id' => PriorityType::inRandomOrder()->first()->id,
            'due_date' => $pastDue
                ? fake()->dateTimeBetween('-30 days', '-1 days')->format('Y-m-d')
                : fake()->dateTimeBetween('now', '+14 days')->format('Y-m-d'),
            'completion_date' => null,
        ];
    }
}
