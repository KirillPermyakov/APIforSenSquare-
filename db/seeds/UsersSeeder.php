<?php

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    /**
     * Run Method.
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'name' => 'John',
                'email' => 'john1488@gmail.com',
                'api_key' => 'f7dfcdb00da761f8e23a76b013ba9549',
                'password_hash' => '$2a$10$96235a93ee2033c3db2f7OWTsGfBJ0LyGGL5jc6FkL0yF.g9ANgL2'
            ],
            [
                'id' => 2,
                'name' => 'Alex',
                'email' => 'alexman@mail.ru',
                'api_key' => 'eaf78cbe2eb41f26596b092f7947c414',
                'password_hash' => '$2a$10$da5ebcc9312392c73330ceXbzyMQr.58upfoeSemCn4G//SqeanQW'
            ],
            [
                'id' => 3,
                'name' => 'Sheldon',
                'email' => 'sheldon@yahoo.com',
                'api_key' => 'eaf78cbe2eb41f26596b092f7947c414',
                'password_hash' => '$2a$10$3a53655ba47b9ea3a21d5uFOo5DEopjnYccUT4N9amRJ3PeA5ji0m'
            ]
        ];

        $users = $this->table('users');
        $users->insert($data)->save();
    }
}
