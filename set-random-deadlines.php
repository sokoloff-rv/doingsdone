<?php

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

function getRandomDeadline(): string {
    $now = new DateTime();
    $now->modify('+' . rand(1, 7) . ' days');
    $now->modify('+' . rand(0, 23) . ' hours');
    $now->modify('+' . rand(0, 59) . ' minutes');

    return $now->format('Y-m-d H:i:s');
}

$user_id = 2;

$stmt = $mysqli->prepare('SELECT id FROM tasks WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result) {
    $current_date = (new DateTime())->format('Y-m-d H:i:s');

    while ($row = $result->fetch_assoc()) {
        $task_id = (int)$row['id'];
        $random_deadline = getRandomDeadline();

        $update_sql = "UPDATE tasks SET deadline = ?, status = 0, creation_date = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param('ssi', $random_deadline, $current_date, $task_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "Задачи пользователя с ID {$user_id} обновлены: новые дедлайны и дата создания установлены.\n";
} else {
    echo "Ошибка при получении задач пользователя $user_id: " . $mysqli->error;
}

$mysqli->close();
