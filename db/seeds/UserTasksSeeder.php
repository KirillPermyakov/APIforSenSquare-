<?php

use Phinx\Seed\AbstractSeed;

class UserTasksSeeder extends AbstractSeed
{
    /**
     * Run Method.
     */
    public function run()
    {
        $data = [
            [
                'user_id' => '1',
                'task_id' => '1'
            ],
            [
                'user_id' => '2',
                'task_id' => '2'
            ],
            [
                'user_id' => '3',
                'task_id' => '3',
            ],
            [
                'user_id' => '3',
                'task_id' => '4'
            ]
        ];

        $userTasks = $this->table('user_tasks');
        $userTasks->insert($data)->save();
    }
}
