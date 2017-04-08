<?php

namespace app\SimpleApi\helpers;


class ApiKeyHelper
{
    /**
     * @return string
     */
    public static function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }
}