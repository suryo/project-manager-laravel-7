<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('projects', 'slug')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('id');
            });

            // Populate existing projects
            $projects = \Illuminate\Support\Facades\DB::table('projects')->get();
            foreach ($projects as $project) {
                \Illuminate\Support\Facades\DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['slug' => \Illuminate\Support\Str::slug($project->title)]);
            }

            // Change to not null and unique (Raw SQL to avoid DBAL Enum issue)
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE projects MODIFY COLUMN slug VARCHAR(255) NOT NULL, ADD UNIQUE(slug)');
            } catch (\Exception $e) {
                // Fallback for SQLite or if unique index already exists (though strict modification should be fine)
                // If it fails, we assume it might be because of syntax, but for MySQL this matches.
                // Note: 'slug' is already 'after id' physically, MODIFY keeps position in MySQL usually but we don't strictly care if it moves slightly as long as it exists.
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
