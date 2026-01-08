<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Pending',
                'color' => 'secondary',
            ],
            [
                'name' => 'In Progress',
                'color' => 'primary',
            ],
            [
                'name' => 'Completed',
                'color' => 'success',
            ],
            [
                'name' => 'On Hold',
                'color' => 'warning',
            ],
            [
                'name' => 'Cancelled',
                'color' => 'danger',
            ],
        ];

        foreach ($statuses as $status) {
            ProjectStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
