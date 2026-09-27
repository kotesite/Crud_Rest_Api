<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS');

require __DIR__ . '/helpers.php';
require __DIR__ . '/connect.php';
require __DIR__ . '/tasks.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// /tasks или /tasks/5
$q = trim($_GET['q'] ?? '', '/');
$params = explode('/', $q);
$type = $params[0];
$id = $params[1] ?? null;

if ($type !== 'tasks' || count($params) > 2) {
    sendError(404, 'Not found');
}

try {
    switch ($method) {
        case 'GET':
            if ($id === null) {
                getTasks($connect);
            } else {
                getTask($connect, $id);
            }
            break;

        case 'POST':
            if ($id !== null) {
                sendError(405, 'Method not allowed');
            }
            addTask($connect, getRequestData());
            break;

        case 'PATCH':
            if ($id === null) {
                sendError(405, 'Method not allowed');
            }
            updateTask($connect, $id, getRequestData());
            break;

        case 'DELETE':
            if ($id === null) {
                sendError(405, 'Method not allowed');
            }
            deleteTask($connect, $id);
            break;

        default:
            sendError(405, 'Method not allowed');
    }
} catch (mysqli_sql_exception $e) {
    sendError(500, 'Database error');
}
