<?php

namespace app\SimpleApi\helpers;

use Exception;
use PDOException;

class PDOConnection
{

    private $conn;
    /**
     * @var PDOConnection
     */
    protected static $_instance = null;

    public static function instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new PDOConnection();
        }
        return self::$_instance;
    }

    protected function __construct()
    {
    }

    public function getConnection()
    {
        include_once(__DIR__ . '/../config.php');
        $conn = null;
        try {
            $conn = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, 'root', DB_PASSWORD);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function __clone()
    {
        return false;
    }

    public function __wakeup()
    {
        return false;
    }

}

?>