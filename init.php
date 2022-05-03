<?php
require_once('helpers.php');
require_once('functions.php');

$database = require_once('database.php');
$connect = mysqli_connect($database['host'], $database['user'], $database['password'], $database['name']);
mysqli_set_charset($connect, "utf8");

if (!$connect) {
    $error = ['error' => mysqli_connect_error()];
    $page_content = include_template('error.php', $error);
    $layout_content = include_template('layout.php', ['page_content' => $page_content]);
    print($layout_content);
}