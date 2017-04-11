<?php

use Phinx\Seed\AbstractSeed;

class TasksSeeder extends AbstractSeed
{
    /**
     * Run Method.
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'task' => 'Create new project'
            ],
            [
                'id' => 2,
                'task' => 'Refactor old project'
            ],
            [
                'id' => 3,
                'task' => 'Check for updates'
            ],
            [
                'id' => 4,
                'task' => 'Delete old package'
            ]
        ];

        $tasks = $this->table('tasks');
        $tasks->insert($data)->save();
    }
}
