<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $users = $this->table('users');
        $users->addColumn('name', 'string', ['limit' => 50])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('password_hash', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('api_key', 'string', ['limit' => 100])
            ->addColumn('status', 'boolean', ['default' => true, 'null' => true])
            ->addIndex(['email'], ['unique' => true])
            ->save();
    }

    /**
     * Migrate down
     */
    public function down()
    {

    }
}
