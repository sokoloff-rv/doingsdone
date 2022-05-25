<?php
require_once('init.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors['email'] = is_filled('email');
    if (!is_filled('email')) {
        $errors['email'] = check_email_validity($connect, $_POST['email']);
    }
    $errors['password'] = is_filled('password');

    if (empty($errors['email']) && check_password($connect, $_POST['email'], $_POST['password'])) {
        $_SESSION['user_id'] = get_user_id($connect, $_POST['email']);
        header('Location: /index.php');
        exit();
    } elseif (!is_filled('password')) {
        $errors['password'] = "Вы ввели неверный email/пароль";
    }
};

$page_content_data = [
    'errors' => $errors
];
$page_content = include_template('auth.php', $page_content_data);

$layout_content_data = [
    'page_content' => $page_content,
    'page_name' => 'Авторизация'
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);
