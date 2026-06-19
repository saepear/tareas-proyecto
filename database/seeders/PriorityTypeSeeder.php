<?php

namespace Database\Seeders;

use App\Models\PriorityType;
use Illuminate\Database\Seeder;

class PriorityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $priorities = [
            ['name' => 'baja'],
            ['name' => 'media'],
            ['name' => 'alta'],
        ];

        foreach ($priorities as $priority) {
            PriorityType::firstOrCreate($priority);
        }
    }
}
