<?php

namespace app\SimpleApi\v1;

use SimpleApi\helpers\DbHandler;
use SimpleApi\helpers\ValidateHelper;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once '../vendor/autoload.php';

$app = new \Slim\App();

$app->post('/register', function (Request $request, Response $response) {
    $requestParams = $request->getParsedBody();
    $data = [
        'error' => null,
        'message' => ''
    ];

    if (!ValidateHelper::validateParams($requestParams, ['name', 'email', 'password'])) {
        $data['error'] = true;
        $data['message'] = 'Required field(s) missing or empty';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $isValidEmail = ValidateHelper::isValidEmail($requestParams['email']);

    if (is_array($isValidEmail)) {
        $newResponse = $response->withJson($isValidEmail, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $result = $db->createUser($requestParams['name'], $requestParams['email'], $requestParams['password']);

    if ($result) {
        $data['error'] = false;
        $data['message'] = 'You are successfully registered';
        $newResponse = $response->withJson($data, 200);
    } else {
        $data['error'] = true;
        $data['message'] = 'Ooops... Something goes wrong, sorry. Try again please.';
        $newResponse = $response->withJson($data, 404);
    }

    return $newResponse;
});

$app->post('/login', function (Request $request, Response $response) {
    $requestParams = $request->getParsedBody();
    $data = [
        'error' => null,
    ];

    if (!ValidateHelper::validateParams($requestParams, ['email', 'password'])) {
        $data['error'] = true;
        $data['message'] = 'Required field(s) missing or empty';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();

    if ($db->checkLogin($requestParams['email'], $requestParams['password'])) {
        $user = $db->getUserByEmail($requestParams['email']);
        if ($user) {
            $data['error'] = false;
            $data['name'] = $user['name'];
            $data['email'] = $user['email'];
            $data['api_key'] = $user['api_key'];
            $newResponse = $response->withJson($data, 200);
            return $newResponse;

        } else {
            $data['error'] = true;
            $data['message'] = 'An error occurred. Please try again';
            $newResponse = $response->withJson($data, 404);
            return $newResponse;
        }
    } else {
        $data['error'] = true;
        $data['message'] = 'Login Failed.Incorrect credentials';
        $newResponse = $response->withJson($data, 200);
    }

    return $newResponse;
});

$app->get('/tasks', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key'])) {
        $data['error'] = true;
        $data['message'] = 'Api key is missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        $data = $db->getAllUserTasks($userId);
        $data['error'] = false;
        $newResponse = $response->withJson($data, 200);
        return $newResponse;
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }
}
);

$app->get('/users', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key'])) {
        $data['error'] = true;
        $data['message'] = 'Api key is missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        $data = $db->getAllUsers();
        $data['error'] = false;
        $newResponse = $response->withJson($data, 200);
        return $newResponse;
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }
});

$app->get('/task/{id}', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key'])) {
        $data['error'] = true;
        $data['message'] = 'Api key is missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        $data = $db->getTaskByIds($request->getAttribute('id'), $userId);
        $data['error'] = false;
        $newResponse = $response->withJson($data, 200);
        return $newResponse;
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }
});

$app->get('/user/{id}', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key'])) {
        $data['error'] = true;
        $data['message'] = 'Api key is missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        $data = $db->getUserById($request->getAttribute('id'));
        $data['error'] = false;
        $newResponse = $response->withJson($data, 200);
        return $newResponse;
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }
});

$app->post('/task', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $postParams = $request->getParsedBody();

    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key']) || !ValidateHelper::validateParams($postParams, ['task'])) {
        $data['error'] = true;
        $data['message'] = 'Required param(s) missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        $taskId = $db->createTask($userId, $postParams['task']);
        if ($taskId) {
            $data['error'] = false;
            $data['message'] = 'Task created successfully';
            $data['task_id'] = $taskId;
            $newResponse = $response->withJson($data, 200);
            return $newResponse;
        } else {
            $data['error'] = true;
            $data['message'] = 'Failed to create task. Please try again';
            $newResponse = $response->withJson($data, 400);
            return $newResponse;
        }
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

});

$app->put('/user/{id}', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $putParams = $request->getParsedBody();

    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key']) || !ValidateHelper::validateParams($putParams, ['name', 'email'])) {
        $data['error'] = true;
        $data['message'] = 'Required param(s) missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $isValidEmail = ValidateHelper::isValidEmail($putParams['email']);

    if (is_array($isValidEmail)) {
        $newResponse = $response->withJson($isValidEmail, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        if ($db->updateUser($putParams['name'], $putParams['email'], $request->getAttribute('id'))) {
            $data['error'] = false;
            $data['message'] = 'User was updated successfully!';
            $newResponse = $response->withJson($data, 200);
            return $newResponse;
        } else {
            $data['error'] = true;
            $data['message'] = 'Failed to update user. Please try again';
            $newResponse = $response->withJson($data, 400);
            return $newResponse;
        }
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

});

$app->put('/task/{id}', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $putParams = $request->getParsedBody();

    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key']) || !ValidateHelper::validateParams($putParams, ['task', 'status'])) {
        $data['error'] = true;
        $data['message'] = 'Required param(s) missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);

    if ($userId) {
        if ($db->updateTask($putParams['task'], $putParams['status'], $request->getAttribute('id'), $userId)) {
            $data['error'] = false;
            $data['message'] = 'Task was updated successfully!';
            $newResponse = $response->withJson($data, 200);
            return $newResponse;
        } else {
            $data['error'] = true;
            $data['message'] = 'Failed to update task. Please try again';
            $newResponse = $response->withJson($data, 400);
            return $newResponse;
        }
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

});

$app->delete('/task/{id}', function (Request $request, Response $response) {
    $getParams = $request->getQueryParams();
    $data = [
        'error' => null
    ];

    if (!ValidateHelper::validateParams($getParams, ['api_key'])) {
        $data['error'] = true;
        $data['message'] = 'Api key is missing';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }

    $db = new DbHandler();
    $userId = $db->isApiKeyExists($getParams['api_key']);
    if ($userId) {
        if ($db->deleteTask($userId, $request->getAttribute('id'))) {
            $data['error'] = false;
            $data['message'] = 'Task was successfully deleted';
            $newResponse = $response->withJson($data, 200);
            return $newResponse;
        } else {
            $data['error'] = true;
            $data['message'] = 'Failed to delete task. Please try again';
            $newResponse = $response->withJson($data, 400);
            return $newResponse;
        }
    } else {
        $data['error'] = true;
        $data['message'] = 'Wrong Api Key. Authorization failed.';
        $newResponse = $response->withJson($data, 400);
        return $newResponse;
    }
});

$app->run();
