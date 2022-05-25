<?php
require_once('init.php');

$projects_list = get_user_projects($connect, $user_id);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors['name'] = is_filled('name');
    if (!is_filled('name')) {
        $errors['name'] = is_unique_name($connect, $_POST['name'], $user_id);
    }

    if (empty($errors['name'])) {
        add_new_project($connect, $_POST['name'], $user_id);
        header('Location: /index.php');
        exit();
    }
};

$page_content_data = [
    'projects' => $projects_list,
    'all_tasks' => get_all_user_tasks($connect, $user_id),
    'errors' => $errors
];
$page_content = include_template('new_project.php', $page_content_data);

if (!isset($_SESSION['user_id'])) {
    $page_content = include_template('guest.php');
}

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => get_user_name($connect, $user_id),
    'page_name' => 'Добавление проекта'
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);
