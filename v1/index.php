<?php

namespace app\SimpleApi\v1;

require_once '../vendor/autoload.php';

use app\SimpleApi\helpers\DbHandler;
use app\SimpleApi\helpers\ResponseHelper;
use app\SimpleApi\helpers\ValidateHelper;
use Slim\Slim;

Slim::registerAutoloader();
$app = new Slim();


$app->post('/register', function () use ($app) {
    ValidateHelper::verifyRequiredParams((['name', 'email', 'password']));
    $response = [];
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    ValidateHelper::validateEmail($email);
    $password = $app->request->post('password');

    $db = new DbHandler();
    $result = $db->createUser($name, $email, $password);

    if ($result) {
        $response['error'] = false;
        $response['message'] = 'You are successfully registered';
        ResponseHelper::echoResponse(201, $response);
    } else {
        $response['error'] = true;
        $response['message'] = 'Sorry error while register';
        ResponseHelper::echoResponse(200, $response);
    }
});

$app->post('/login', function () use ($app) {
    ValidateHelper::verifyRequiredParams(['email', 'password']);
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = [];

    $db = new DbHandler();
    if ($db->checkLogin($email, $password)) {
        $user = $db->getUserByEmail($email);
        if ($user) {
            $response['error'] = false;
            $response['name'] = $user['name'];
            $response['email'] = $user['email'];
            $response['api_key'] = $user['api_key'];
        } else {
            $response['error'] = false;
            $response['message'] = 'An error occurred. Please try again';

        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials';
    }
    ResponseHelper::echoResponse(200, $response);
});


$app->run();
