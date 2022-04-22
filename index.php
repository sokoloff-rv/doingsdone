<?php
require_once('helpers.php');
require_once('functions.php');
$show_complete_tasks = rand(0, 1);
$projects = ["Входящие", "Учеба", "Работа", "Домашние дела", "Авто"];
$tasks = [
    [
        'title' => 'Собеседование в IT компании',
        'date' => '01.12.2019',
        'category' => 'Работа',
        'complete' => false
    ],
    [
        'title' => 'Выполнить тестовое задание',
        'date' => '	25.12.2019',
        'category' => 'Работа',
        'complete' => false
    ],
    [
        'title' => 'Сделать задание первого раздела',
        'date' => '21.12.2019',
        'category' => 'Учеба',
        'complete' => true
    ],
    [
        'title' => 'Встреча с другом',
        'date' => '22.12.2019',
        'category' => 'Входящие',
        'complete' => false
    ],
    [
        'title' => 'Купить корм для кота',
        'date' => null,
        'category' => 'Домашние дела',
        'complete' => false
    ],
    [
        'title' => 'Заказать пиццу',
        'date' => null,
        'category' => 'Домашние дела',
        'complete' => false
    ],
];

$page_content_data = [
    'projects' => $projects, 
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks
];
$page_content = include_template('main.php', $page_content_data);

$layout_content_data = [
    'page_content' => $page_content,
    'user_name' => 'Константин',
    'page_name' => 'Дела в порядке'
    
];
$layout_content = include_template('layout.php', $layout_content_data);

print($layout_content);