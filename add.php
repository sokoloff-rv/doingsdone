<?php
require_once('init.php');

$projects_list = get_user_projects($connect, $user_id);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors['name'] = is_filled('name');
    $errors['project'] = is_project_exist($projects_list, 'project');
    $errors['date'] = is_correct_date('date');

    $deadline = null;
    if (isset($_POST['date'])) {
        $deadline = $_POST['date'];
    }

    $file_link = null;
    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        $file_name = 'file-' . uniqid() . '_' . $_FILES['file']['name'];
        $file_path = __DIR__ . '/uploads/';
        $file_url = '/uploads/' . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
        $file_link = '/uploads/' . $file_name;
    }

    if (empty($errors['name']) && empty($errors['project']) && empty($errors['date'])) {
        add_new_task($connect, $_POST['name'], $file_link, $deadline, $_POST['project'], $user_id);
        header('Location: /index.php');
        exit();
    }
};

$page_content_data = [
    'projects' => $projects_list,
    'all_tasks' => get_all_user_tasks($connect, $user_id),
    'errors' => $errors,
    'connect' => $connect,
    'user_id' => $user_id
];
$page_content = include_template('new_task.php', $page_content_data);

if (!isset($_SESSION['user_id'])) {
    $page_content = include_template('guest.php');
}

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => get_user_name($connect, $user_id),
    'page_name' => 'Добавление задачи'
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);
