<?php
require_once('init.php');

if (!check_csrf_token(filter_input(INPUT_GET, 'token'))) {
    http_response_code(403);
    exit('Ошибка проверки безопасности.');
}

unset($_SESSION['user_id']);
header('Location: /');
