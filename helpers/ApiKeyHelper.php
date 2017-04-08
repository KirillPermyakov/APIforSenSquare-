<?php

namespace app\SimpleApi\helpers;


class ApiKeyHelper
{
    public static function generateApiKey()
    {
        return md5(uniqid(rand(), true));
    }
}