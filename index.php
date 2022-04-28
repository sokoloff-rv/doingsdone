<?php
require_once('init.php');
$show_complete_tasks = rand(0, 1);
$user_id = 1;

$page_content_data = [
    'projects' => get_user_projects($connect, $user_id),
    'tasks' => get_user_tasks($connect, $user_id),
    'show_complete_tasks' => $show_complete_tasks
];
$page_content = include_template('main.php', $page_content_data);

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => get_user_name($connect, $user_id),
    'page_name' => 'Дела в порядке'    
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);