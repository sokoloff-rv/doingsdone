<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Этот скрипт можно запускать только из командной строки.');
}

$db_config = require __DIR__ . '/database.php';

$mysqli = new mysqli(
    $db_config['host'],
    $db_config['user'],
    $db_config['password'],
    $db_config['name']
);

if ($mysqli->connect_error) {
    die('Ошибка подключения: ' . $mysqli->connect_error);
}

/**
 * Возвращает дедлайн (в 23:59) для заданной категории фильтра меню,
 * чтобы соответствующая вкладка гарантированно содержала задачи.
 *
 * @param string $category одна из категорий: overdue, today, tomorrow, future, done
 *
 * @return string дедлайн в формате 'Y-m-d H:i:s'
 */
function deadline_for_category(string $category): string {
    $date = new DateTime();

    switch ($category) {
        case 'overdue':
        case 'done':
            $date->modify('-' . rand(1, 7) . ' days');
            break;
        case 'today':
            break;
        case 'tomorrow':
            $date->modify('+1 day');
            break;
        case 'future':
        default:
            $date->modify('+' . rand(2, 7) . ' days');
            break;
    }

    return $date->format('Y-m-d 23:59:00');
}

// Категории перебираются циклически, поэтому при достаточном количестве задач
// каждая вкладка меню всегда получает хотя бы одну задачу. Категория 'done' —
// это выполненные задачи в прошлом (status = 1), остальные не выполнены (status = 0).
$categories = [
    ['type' => 'overdue',  'status' => 0],
    ['type' => 'today',    'status' => 0],
    ['type' => 'tomorrow', 'status' => 0],
    ['type' => 'future',   'status' => 0],
    ['type' => 'done',     'status' => 1],
];

$user_id = isset($argv[1]) ? (int) $argv[1] : 2;

$stmt = $mysqli->prepare('SELECT id FROM tasks WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result) {
    $current_date = (new DateTime())->format('Y-m-d H:i:s');

    $index = 0;
    while ($row = $result->fetch_assoc()) {
        $task_id = (int)$row['id'];
        $category = $categories[$index % count($categories)];
        $deadline = deadline_for_category($category['type']);
        $status = $category['status'];

        $update_sql = "UPDATE tasks SET deadline = ?, status = ?, creation_date = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param('sisi', $deadline, $status, $current_date, $task_id);
        $stmt->execute();
        $stmt->close();

        $index++;
    }

    echo "Задачи пользователя с ID {$user_id} обновлены ({$index} шт.): дедлайны на 23:59 распределены по всем вкладкам меню, часть прошлых задач помечена выполненными.\n";
} else {
    echo "Ошибка при получении задач пользователя $user_id: " . $mysqli->error;
}

$mysqli->close();
