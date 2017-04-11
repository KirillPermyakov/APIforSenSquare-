<?php

use Phinx\Migration\AbstractMigration;

class CreateTasksTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $tasks = $this->table('tasks');
        $tasks->addColumn('task', 'string', ['limit' => 300, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 100, 'default' => 'new'])
            ->addIndex(['task'], ['unique' => true])
            ->save();
    }

    /**
     * Migrate down.
     */
    public function down()
    {

    }
}
