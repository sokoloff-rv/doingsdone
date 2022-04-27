<?php
require_once('functions.php');
$database = require_once('database.php');
$connect = mysqli_connect($database['host'], $database['user'], $database['password'], $database['name']);
mysqli_set_charset($connect, "utf8");