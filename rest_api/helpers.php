<?php

const TITLE_MAX = 255;
const STATUS_MAX = 40;

function sendJson($code, $data) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function sendError($code, $message) {
    sendJson($code, ['status' => false, 'message' => $message]);
}

// тело запроса: json, либо обычная форма
function getRequestData() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (is_array($data)) {
        return $data;
    }
    if (!empty($_POST)) {
        return $_POST;
    }

    parse_str($raw, $data);
    return $data;
}

function getId($id) {
    $id = filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id === false) {
        sendError(400, 'ID должен быть целым положительным числом');
    }
    return $id;
}

function getField($data, $name) {
    if (!isset($data[$name])) {
        return null;
    }
    return trim((string)$data[$name]);
}

function checkLength($name, $value, $max) {
    if (mb_strlen($value) > $max) {
        sendError(400, "Поле $name слишком длинное (макс. $max символов)");
    }
}

function validateNewTask($data) {
    $title = getField($data, 'title');
    $description = getField($data, 'description');
    $status = getField($data, 'status');

    if ($title === null || $title === '' || $description === null || $description === '') {
        sendError(400, 'Поля title и description обязательны');
    }
    if ($status === null || $status === '') {
        $status = 'new';
    }

    checkLength('title', $title, TITLE_MAX);
    checkLength('status', $status, STATUS_MAX);

    return ['title' => $title, 'description' => $description, 'status' => $status];
}

// для PATCH - берем только те поля, которые пришли
function validateTaskUpdate($data) {
    $fields = [];

    foreach (['title', 'description', 'status'] as $name) {
        $value = getField($data, $name);
        if ($value === null) {
            continue;
        }
        if ($value === '') {
            sendError(400, "Поле $name не может быть пустым");
        }
        $fields[$name] = $value;
    }

    if (empty($fields)) {
        sendError(400, 'Нет полей для обновления');
    }

    if (isset($fields['title'])) {
        checkLength('title', $fields['title'], TITLE_MAX);
    }
    if (isset($fields['status'])) {
        checkLength('status', $fields['status'], STATUS_MAX);
    }

    return $fields;
}
