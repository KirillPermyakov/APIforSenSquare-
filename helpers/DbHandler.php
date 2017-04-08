<?php

namespace app\SimpleApi\helpers;

class DbHandler
{
    /**
     * @var null|\PDO
     */
    private $conn;

    /**
     * DbHandler constructor.
     */
    function __construct()
    {
        $this->conn = PDOConnection::instance()->getConnection();
    }


    /**
     * Get the user by id
     * @param $id int
     * @return bool|array
     */
    public function getUserById($id)
    {
        $stmt = $this->conn->prepare('SELECT name, email, api_key, status FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        }
        return false;
    }

    /**
     * Getting all users list
     * @return array|bool
     */
    public function getAllUsers()
    {
        $stmt = $this->conn->prepare('SELECT * FROM users');
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get the user by email
     * @param $email string
     * @return bool|array
     */
    public function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare('SELECT name, email, api_key, status FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        }
        return false;
    }


    /**
     * Get the user api_key by user id
     * @param $id int
     * @return bool|string
     */
    public function getApiKeyById($id)
    {
        $stmt = $this->conn->prepare('SELECT api_key FROM users WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            return $result['api_key'];
        }
        return false;
    }

    /**
     * Get the user id by api_key
     * @param $apiKey string
     * @return bool | string
     */
    public function getUserIdByApiKey($apiKey)
    {
        $stmt = $this->conn->prepare('SELECT id FROM users WHERE api_key=:api_key');
        $stmt->bindParam(':api_key', $apiKey);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            return $result['id'];
        }
        return false;
    }

    /**
     * Check if user exists in DB already
     * (need for creation of user for example)
     * @param $email string
     * @return bool
     */
    private function isUserExists($email)
    {
        $stmt = $this->conn->prepare('SELECT id FROM users WHERE email=:email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if (!$stmt->fetch()) {
            return false;
        }
        return true;
    }

    /**
     * Check if api key exists in DB
     * @param $apiKey string
     * @return bool
     */
    public function isApiKeyExists($apiKey)
    {
        $stmt = $this->conn->prepare('SELECT id FROM users WHERE api_key=:api_key');
        $stmt->bindParam(':api_key', $apiKey);
        $stmt->execute();

        if (!$stmt->fetch()) {
            return false;
        }
        return true;
    }

    /**
     * Creating user
     * @param $name string
     * @param $email string
     * @param $password string
     * @return bool
     */
    public function createUser($name, $email, $password)
    {
        if (!$this->isUserExists($email)) {
            $passwordHash = PassHash::hash($password);
            $apiKey = ApiKeyHelper::generateApiKey();
            $stmt = $this->conn->prepare('INSERT INTO users(name, email, password_hash, api_key, status) VALUES (:name, :email, :password_hash, :api_key, 1)');
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':api_key', $apiKey);
            $result = $stmt->execute();

            if ($result) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Create task for user (insert into user_tasks table)
     * @param $userId int
     * @param $taskId int
     * @return bool
     */
    public function createTaskForUser($userId, $taskId)
    {
        $stmt = $this->conn->prepare('INSERT INTO user_tasks(user_id, task_id) VALUES(:user_id, :task_id)');
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':task_id', $taskId);
        $result = $stmt->execute();

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Create task (insert into table tasks and link task with user by inserting in user_tasks table)
     * @param $userId int
     * @param $task string
     * @return bool
     */
    public function createTask($userId, $task)
    {
        $stmt = $this->conn->prepare('INSERT INTO tasks(task) VALUES(:task)');
        $stmt->bindParam(':task', $task);
        $result = $stmt->execute();

        if ($result) {
            return $this->createTaskForUser($userId, $this->conn->lastInsertId());
        }

        return false;
    }

    /**
     * Getting one task by task_id and user_id
     * @param $taskId int
     * @param $userId int
     * @return bool | array
     */
    public function getTaskByIds($taskId, $userId)
    {
        $stmt = $this->conn->prepare('SELECT t.* from tasks t, user_tasks ut 
                                      WHERE t.id = :task_id AND ut.task_id = t.id 
                                      AND ut.user_id = :user_id');
        $stmt->bindParam(':task_id', $taskId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);


        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Getting all user tasks by user_id
     * @param $userId int
     * @return bool|mixed
     */
    public function getAllUserTasks($userId)
    {
        $stmt = $this->conn->prepare('SELECT t.* FROM tasks t, user_tasks ut WHERE t.id = ut.task_id AND ut.user_id = :user_id');
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);


        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Updating single task
     * @param $task string
     * @param $status string
     * @param $taskId int
     * @param $userId int
     * @return bool
     */
    public function updateTask($task, $status, $taskId, $userId)
    {
        $stmt = $this->conn->prepare('UPDATE tasks t, user_tasks ut SET t.task = :task, t.status = :status WHERE t.id = :task_id AND t.id = ut.task_id AND ut.user_id = :user_id');
        $stmt->bindParam(':task', $task);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':task_id', $taskId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Deleting single task
     * @param $userId int
     * @param $taskId int
     * @return bool
     */
    public function deleteTask($userId, $taskId)
    {
        $stmt = $this->conn->prepare("DELETE t FROM tasks t, user_tasks ut WHERE t.id = :task_id AND ut.task_id = t.id AND ut.user_id = :user_id");
        $stmt->bindParam('user_id', $userId);
        $stmt->bindParam('task_id', $taskId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Updating single user
     * @param $name string
     * @param $email string
     * @param $userId int
     * @return bool
     */
    public function updateUser($name, $email, $userId)
    {
        $stmt = $this->conn->prepare("UPDATE users SET name = :name, email =:email WHERE id=:id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Check login with email and password
     * @param $email string
     * @param $password string
     * @return bool
     */
    public function checkLogin($email, $password)
    {
        $stmt = $this->conn->prepare('SELECT password_hash FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result) {
            if (PassHash::checkPassword($result['password_hash'], $password)) {
                return true;
            }
            return false;
        }
        return false;
    }


}
