<?php
require_once('init.php');

unset($_SESSION['user_id']);
header('Location: /index.php');
