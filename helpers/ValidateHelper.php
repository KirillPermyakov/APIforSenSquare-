<?php

namespace app\SimpleApi\helpers;

class ValidateHelper
{
    /** Validates Array of Required Params
     * @param $paramsArray array
     * @param $nameOfParams array
     * @return bool
     */
    public static function validateParams($paramsArray, $nameOfParams)
    {
        foreach ($nameOfParams as $nameOfParam) {
            if (!array_key_exists($nameOfParam, $paramsArray)) {
                return false;
            }
        }

        foreach ($paramsArray as $param) {
            if (empty($param)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate email
     * @param $email string
     * @return bool
     */
    public static function isValidEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response["error"] = true;
            $response["message"] = 'Email address is not valid';
            return $response;
        } else {
            return false;
        }
    }

}
