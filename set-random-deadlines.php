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
 * Возвращает дедлайн (в 00:00:00) для заданной категории фильтра меню,
 * чтобы соответствующая вкладка гарантированно содержала задачи.
 *
 * @param string $category одна из категорий: overdue, today, tomorrow, future
 *
 * @return string дедлайн в формате 'Y-m-d H:i:s'
 */
function deadline_for_category(string $category): string {
    $date = new DateTime();

    switch ($category) {
        case 'overdue':
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

    return $date->format('Y-m-d 00:00:00');
}

// Категории перебираются циклически, поэтому при достаточном количестве задач
// каждая вкладка меню («Просроченные», «Повестка дня», «Завтра», «Все задачи»)
// всегда получает хотя бы одну задачу.
$categories = ['overdue', 'today', 'tomorrow', 'future'];

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
        $deadline = deadline_for_category($category);

        $update_sql = "UPDATE tasks SET deadline = ?, status = 0, creation_date = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param('ssi', $deadline, $current_date, $task_id);
        $stmt->execute();
        $stmt->close();

        $index++;
    }

    echo "Задачи пользователя с ID {$user_id} обновлены ({$index} шт.): дедлайны распределены по всем вкладкам меню.\n";
} else {
    echo "Ошибка при получении задач пользователя $user_id: " . $mysqli->error;
}

$mysqli->close();
