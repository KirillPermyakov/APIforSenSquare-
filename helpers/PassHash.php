<?php

namespace app\SimpleApi\helpers;

class PassHash
{
    /**
     * @var string
     */
    private static $algo = '$2a';
    /**
     * @var string
     */
    private static $cost = '$10';

    /**
     * @return string
     */
    public static function uniqueSalt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    /**
     * @param $password
     * @return string
     */
    public static function hash($password)
    {

        return crypt($password, self::$algo . self::$cost . '$' . self::uniqueSalt());
    }

    /**
     * @param $hash
     * @param $password
     * @return bool
     */
    public static function checkPassword($hash, $password)
    {
        $fullSalt = substr($hash, 0, 29);
        $newHash = crypt($password, $fullSalt);
        return ($hash == $newHash);
    }

}
