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
            ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
            ->addForeignKey('task_id', 'tasks', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION'])
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
