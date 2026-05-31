<?php
require_once('init.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_csrf_valid()) {
        http_response_code(403);
        exit('Ошибка проверки безопасности формы. Обновите страницу и попробуйте снова.');
    }

    $email_error = is_filled('email');
    if (!$email_error) {
        $email_error = check_email_validity($_POST['email']);
    }
    $errors['email'] = $email_error;
    $errors['password'] = is_filled('password');

    if (empty($errors['email']) && check_password($connect, $_POST['email'], $_POST['password'])) {
        $_SESSION['user_id'] = get_user_id($connect, $_POST['email']);
        header('Location: /');
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
