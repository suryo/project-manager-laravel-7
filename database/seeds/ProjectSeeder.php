<?php

use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('projects')->truncate();
        Schema::enableForeignKeyConstraints();

        $projects = [
            ['title' => 'sidar', 'user_id' => 8, 'department_id' => null, 'status_id' => 1],
            ['title' => 'sda.co.id', 'user_id' => 6, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sachio.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 5],
            ['title' => 'beta.sachio.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 1],
            ['title' => 'tokosda.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'metaquiphose.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'guestbook.sda.id', 'user_id' => 7, 'department_id' => null, 'status_id' => 3],
            ['title' => 'prism.sda.id', 'user_id' => 7, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sdatools.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sangbumi.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sda.co.id/dashboard', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sidar.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'crimson.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sda.id/siamoi', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'vcard.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'wms.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'sda.co.id/sdalink', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'Test Project s', 'user_id' => 1, 'department_id' => null, 'status_id' => 1],
            ['title' => 'indracostore.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'Indraco.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3],
            ['title' => 'supresso.co.id', 'user_id' => 5, 'department_id' => 4, 'status_id' => 1],
            ['title' => 'Test Project d', 'user_id' => 1, 'department_id' => 2, 'status_id' => 1],
        ];

        foreach ($projects as $proj) {
            Project::create([
                'title' => $proj['title'],
                'slug' => Str::slug($proj['title']),
                'user_id' => $proj['user_id'],
                'department_id' => $proj['department_id'],
                'project_status_id' => $proj['status_id'],
                'description' => 'Description for ' . $proj['title'],
                'start_date' => now(),
                'budget' => rand(1000, 5000),
            ]);
        }
    }
}
