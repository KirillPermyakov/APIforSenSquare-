<?php

namespace app\SimpleApi\helpers;

use Slim\Slim;

class ResponseHelper
{
    public static function echoResponse($status_code, $response)
    {
        $app = Slim::getInstance();
        // Http response code
        $app->status($status_code);

        // setting response content type to json
        $app->contentType('application/json');

        echo json_encode($response);
    }
}