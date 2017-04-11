<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTasksTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $usersTasks = $this->table('user_tasks');
        $usersTasks->addColumn('user_id', 'integer', ['null' => false])
            ->addColumn('task_id', 'integer', ['null' => false])
            ->addForeignKey('user_id', 'users', 'id')
            ->addForeignKey('task_id', 'tasks', 'id')
            ->addIndex(['task_id', 'user_id'], ['unique' => true])
            ->save();
    }

    /**
     * Migrate down.
     */
    public function down()
    {

    }
}
