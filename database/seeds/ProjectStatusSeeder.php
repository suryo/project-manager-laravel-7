<?php

use Illuminate\Database\Seeder;
use App\Models\ProjectStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectStatusSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('project_statuses')->truncate();
        Schema::enableForeignKeyConstraints();

        $statuses = [
            ['id' => 1, 'name' => 'Pending', 'color' => 'secondary'],
            ['id' => 2, 'name' => 'In Progress', 'color' => 'primary'],
            ['id' => 3, 'name' => 'Completed', 'color' => 'success'],
            ['id' => 4, 'name' => 'On Hold', 'color' => 'warning'],
            ['id' => 5, 'name' => 'Cancelled', 'color' => 'danger'],
            ['id' => 6, 'name' => 'Inactive', 'color' => 'dark'],
        ];

        foreach ($statuses as $status) {
            ProjectStatus::create($status);
        }
    }
}
