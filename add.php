<?php
require_once('init.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit();
}

$projects_list = get_user_projects($connect, $user_id);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_csrf_valid()) {
        http_response_code(403);
        exit('Ошибка проверки безопасности формы. Обновите страницу и попробуйте снова.');
    }

    $errors['name'] = is_filled('name');
    $errors['project'] = is_project_exist($projects_list, 'project');
    $errors['date'] = is_correct_date('date');

    $deadline = null;
    if (isset($_POST['date'])) {
        $deadline = $_POST['date'];
    }

    $file_link = null;
    if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        $max_file_size = 10 * 1024 * 1024; // 10 МБ
        $forbidden_extensions = ['php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'pht', 'phar', 'cgi', 'pl', 'sh', 'exe', 'htaccess'];
        $original_name = basename($_FILES['file']['name']);
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if ($_FILES['file']['size'] > $max_file_size) {
            $errors['file'] = 'Размер файла не должен превышать 10 МБ! ';
        } elseif (in_array($extension, $forbidden_extensions, true)) {
            $errors['file'] = 'Загрузка файлов такого типа запрещена! ';
        } else {
            $file_name = 'file-' . uniqid() . '_' . $original_name;
            $file_path = __DIR__ . '/uploads/';
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
            $file_link = '/uploads/' . $file_name;
        }
    }

    if (empty($errors['name']) && empty($errors['project']) && empty($errors['date']) && empty($errors['file'])) {
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

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => get_user_name($connect, $user_id),
    'page_name' => 'Добавление задачи'
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);
