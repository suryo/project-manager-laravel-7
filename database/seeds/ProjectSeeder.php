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
        DB::table('tasks')->truncate();
        DB::table('task_user')->truncate();
        DB::table('comments')->truncate();
        DB::table('poac_logs')->truncate();
        Schema::enableForeignKeyConstraints();

        $projects = [
            ['title' => 'sidar', 'user_id' => 8, 'department_id' => null, 'status_id' => 1, 'pic_id' => 8],
            ['title' => 'sda.co.id', 'user_id' => 6, 'department_id' => null, 'status_id' => 3, 'pic_id' => 6],
            ['title' => 'sachio.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 5, 'pic_id' => 1],
            ['title' => 'beta.sachio.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 1, 'pic_id' => 1],
            ['title' => 'tokosda.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 2],
            ['title' => 'metaquiphose.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 3],
            ['title' => 'guestbook.sda.id', 'user_id' => 7, 'department_id' => null, 'status_id' => 3, 'pic_id' => 7],
            ['title' => 'prism.sda.id', 'user_id' => 7, 'department_id' => null, 'status_id' => 3, 'pic_id' => 7],
            ['title' => 'sdatools.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 4],
            ['title' => 'sangbumi.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 5],
            ['title' => 'sda.co.id/dashboard', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 1],
            ['title' => 'sidar.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 1],
            ['title' => 'crimson.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 2],
            ['title' => 'sda.id/siamoi', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 3],
            ['title' => 'vcard.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 4],
            ['title' => 'wms.sda.id', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 5],
            ['title' => 'sda.co.id/sdalink', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 6],
            ['title' => 'Test Project s', 'user_id' => 1, 'department_id' => null, 'status_id' => 1, 'pic_id' => 1],
            ['title' => 'indracostore.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 7],
            ['title' => 'Indraco.com', 'user_id' => 1, 'department_id' => null, 'status_id' => 3, 'pic_id' => 8],
            ['title' => 'supresso.co.id', 'user_id' => 5, 'department_id' => 4, 'status_id' => 1, 'pic_id' => 5],
            ['title' => 'Test Project d', 'user_id' => 1, 'department_id' => 2, 'status_id' => 1, 'pic_id' => 1],
        ];

        foreach ($projects as $proj) {
            Project::create([
                'title' => $proj['title'],
                'slug' => Str::slug($proj['title']),
                'user_id' => $proj['user_id'],
                'department_id' => $proj['department_id'],
                'project_status_id' => $proj['status_id'],
                'pic_id' => $proj['pic_id'] ?? null,
                'description' => 'Description for ' . $proj['title'],
                'start_date' => now(),
                'budget' => rand(1000, 5000),
            ]);
        }
    }
}
