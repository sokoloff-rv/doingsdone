<?php
require_once('helpers.php');
require_once('init.php');

$show_complete_tasks = rand(0, 1);
$user_id = 2;

if (!$connect) {
    $error = mysqli_connect_error();
    $page_content = include_template('error.php', ['error' => $error]);    
}
else {
    $sql_users = "SELECT name FROM users WHERE id = $user_id";
    $user = mysqli_fetch_assoc(mysqli_query($connect, $sql_users));

    $sql_projects = "SELECT title FROM projects WHERE user_id = $user_id";
    $projects = mysqli_fetch_all(mysqli_query($connect, $sql_projects), MYSQLI_ASSOC);

    $sql_tasks = "SELECT status, t.title, deadline, p.title project FROM tasks t JOIN projects p ON project_id = p.id WHERE t.user_id = $user_id";
    $tasks = mysqli_fetch_all(mysqli_query($connect, $sql_tasks), MYSQLI_ASSOC);

    $page_content_data = [
        'projects' => $projects, 
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks
    ];
    $page_content = include_template('main.php', $page_content_data);
}

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => $user['name'],
    'page_name' => 'Дела в порядке'    
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);