<?php

function getTasks($connect) {
    $stmt = mysqli_prepare($connect, "SELECT * FROM `tasks` ORDER BY `id`");
    mysqli_stmt_execute($stmt);
    $tasks = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

    sendJson(200, $tasks);
}

function findTask($connect, $id) {
    $stmt = mysqli_prepare($connect, "SELECT * FROM `tasks` WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return $task ?: null;
}

function getTask($connect, $id) {
    $id = getId($id);
    $task = findTask($connect, $id);

    if (!$task) {
        sendError(404, 'Task not found');
    }
    sendJson(200, $task);
}

function addTask($connect, $data) {
    $task = validateNewTask($data);

    $stmt = mysqli_prepare($connect, "INSERT INTO `tasks` (`title`, `description`, `status`) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sss', $task['title'], $task['description'], $task['status']);
    mysqli_stmt_execute($stmt);

    $id = mysqli_insert_id($connect);
    sendJson(201, findTask($connect, $id));
}

function updateTask($connect, $id, $data) {
    $id = getId($id);
    $fields = validateTaskUpdate($data);

    if (!findTask($connect, $id)) {
        sendError(404, 'Task not found');
    }

    // собираем SET только из пришедших полей, имена полей берутся из белого списка
    $set = [];
    $values = [];
    foreach ($fields as $name => $value) {
        $set[] = "`$name` = ?";
        $values[] = $value;
    }
    $values[] = $id;

    $sql = "UPDATE `tasks` SET " . implode(', ', $set) . " WHERE `id` = ?";
    $types = str_repeat('s', count($fields)) . 'i';

    $stmt = mysqli_prepare($connect, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$values);
    mysqli_stmt_execute($stmt);

    sendJson(200, findTask($connect, $id));
}

function deleteTask($connect, $id) {
    $id = getId($id);

    $stmt = mysqli_prepare($connect, "DELETE FROM `tasks` WHERE `id` = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        sendError(404, 'Task not found');
    }
    sendJson(200, ['status' => true, 'message' => 'Task is deleted']);
}
