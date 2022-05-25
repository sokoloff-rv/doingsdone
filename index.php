<?php
require_once('init.php');

$selected_project_id = filter_input(INPUT_GET, 'project_id');
$search_phrase = filter_input(INPUT_GET, 'search');
$task_id = filter_input(INPUT_GET, 'task_id');
$task_status = filter_input(INPUT_GET, 'check');
$task_deadline = filter_input(INPUT_GET, 'deadline');

if (isset($_GET['show_completed'])) {
    $show_complete_tasks = filter_input(INPUT_GET, 'show_completed');
    setcookie('show_complete_tasks', $show_complete_tasks);
} elseif (isset($_COOKIE['show_complete_tasks'])) {
    $show_complete_tasks = $_COOKIE['show_complete_tasks'];
} else {
    $show_complete_tasks = 0;
}

if (isset($selected_project_id)) {
    $visible_tasks = get_user_tasks_by_project($connect, $selected_project_id, $user_id);
} elseif (isset($task_deadline)) {
    $visible_tasks = get_user_tasks_by_deadline($connect, $task_deadline, $user_id);
} elseif (strlen($search_phrase)) {
    $visible_tasks = get_user_tasks_by_search($connect, $search_phrase, $user_id);
} else {
    $visible_tasks = get_all_user_tasks($connect, $user_id);
}

if (isset($task_id) && isset($task_status)) {
    mark_task_completed($connect, $task_id, $task_status, $user_id);
}

$page_content_data = [
    'projects' => get_user_projects($connect, $user_id),
    'selected_project_id' => $selected_project_id,
    'visible_tasks' => $visible_tasks,
    'show_complete_tasks' => $show_complete_tasks,
    'connect' => $connect,
    'user_id' => $user_id
];
$page_content = include_template('main.php', $page_content_data);

if (!isset($_SESSION['user_id'])) {
    $page_content = include_template('guest.php');
}

if ($selected_project_id && empty($visible_tasks)) {
    http_response_code(404);
    $error = ['error' => 'Код ошибки: 404'];
    $page_content = include_template('error.php', $error);
}

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => get_user_name($connect, $user_id),
    'page_name' => 'Дела в порядке'
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);
