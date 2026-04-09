<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
        $projects = [
            [
                'name'        => 'Website Redesign',
                'description' => 'Full redesign of the company marketing site.',
            ],
            [
                'name'        => 'Mobile App',
                'description' => 'iOS and Android task manager companion app.',
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);

            $tasks = match ($project->name) {
                'Website Redesign' => [
                    'Gather design requirements',
                    'Create wireframes',
                    'Review with stakeholders',
                    'Build frontend components',
                    'Write copy for all pages',
                ],
                'Mobile App' => [
                    'Define MVP feature set',
                    'Set up React Native project',
                    'Implement authentication',
                    'Build task list screen',
                ],
                default => [],
            };

            foreach ($tasks as $priority => $name) {
                Task::create([
                    'project_id' => $project->id,
                    'name'       => $name,
                    'priority'   => $priority + 1,
                ]);
            }
        }

       
        $unassigned = [
            'Read Laravel 11 release notes',
            'Update development dependencies',
            'Review open pull requests',
        ];

        foreach ($unassigned as $priority => $name) {
            Task::create([
                'project_id' => null,
                'name'       => $name,
                'priority'   => $priority + 1,
            ]);
        }
    }
}
