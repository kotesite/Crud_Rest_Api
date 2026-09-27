<?php

$config = require __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $connect = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database']);
    mysqli_set_charset($connect, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    sendJson(500, ['status' => false, 'message' => 'Database connection error']);
}
