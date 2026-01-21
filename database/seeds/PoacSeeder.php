<?php

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Task;

class PoacSeeder extends Seeder
{
    public function run()
    {
        $projects = Project::all();
        
        foreach ($projects as $project) {
            $project->update([
                'mgmt_phase' => ['Planning', 'Organizing', 'Actuating', 'Controlling'][rand(0, 3)],
                'mgmt_planning_notes' => '### Planning Roadmap\n- Finalize project scope and requirements.\n- Set milestones for Q1 and Q2.\n- Risk assessment for technical stack implementation.',
                'mgmt_organizing_notes' => '### Resource Allocation\n- Lead Developer assigned to core architecture.\n- Designer focused on UI/UX mockups.\n- Content team coordinating with stakeholders.',
                'mgmt_actuating_notes' => '### Implementation Strategy\n- Sprints running bi-weekly.\n- Daily stand-ups to track minor blockers.\n- Continuous integration pipeline active.',
                'mgmt_controlling_notes' => '### Quality & Monitoring\n- Code review is mandatory before merge.\n- Performance metrics monitored via real-time dashboard.\n- Feedback loop with department heads every Friday.'
            ]);

            $departmentUsers = $project->department ? $project->department->members->pluck('id')->toArray() : \App\Models\User::pluck('id')->toArray();

            foreach ($project->tasks as $task) {
                $task->update([
                    'mgmt_phase' => ['Planning', 'Organizing', 'Actuating', 'Controlling'][rand(0, 3)],
                    'mgmt_notes' => 'This task is prioritized for the ' . $project->mgmt_phase . ' phase. Ensure all documentation is updated.'
                ]);

                if (!empty($departmentUsers)) {
                    // Assign 1-2 random users to this task
                    $numAssignees = min(count($departmentUsers), rand(1, 2));
                    $randomUserIds = array_rand(array_flip($departmentUsers), $numAssignees);
                    $task->assignees()->sync(is_array($randomUserIds) ? $randomUserIds : [$randomUserIds]);
                }
            }
        }
    }
}
