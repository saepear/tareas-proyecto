<?php

namespace Database\Seeders;

use App\Models\TaskState;
use Illuminate\Database\Seeder;

class TaskStateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            ['name' => 'pending'],
            ['name' => 'in-progress'],
            ['name' => 'completed'],
        ];

        foreach ($states as $state) {
            TaskState::firstOrCreate($state);
        }
    }
}
