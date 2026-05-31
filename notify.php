<?php
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Этот скрипт можно запускать только из командной строки.');
}

require_once("vendor/autoload.php");
require_once("init.php");

$config = require __DIR__ . '/config.php';
$transport = Transport::fromDsn($config['mail_dsn']);

$initial_data = get_users_today_tasks($connect);
$processed_data = [];
foreach ($initial_data as $user_data) {
    $processed_data[$user_data["id"]]["name"] = $user_data["name"];
    $processed_data[$user_data["id"]]["email"] = $user_data["email"];
    $processed_data[$user_data["id"]]["tasks"][] = [
        "title" => $user_data["title"],
        "deadline" => $user_data["deadline"]
    ];
}

foreach ($processed_data as $data) {
    $message = new Email();
    $message->to($data['email']);
    $message->from($config['mail_from']);
    $message->subject("Уведомление от сервиса «Дела в порядке»");
    $message_text = "Уважаемый, {$data['name']}.\n";
    if (count($data["tasks"]) === 1) {
        $message_text = $message_text."У вас запланирована задача:\n";
    } elseif (count($data["tasks"]) > 1) {
        $message_text = $message_text."У вас запланированы задачи:\n";
    }
    foreach ($data["tasks"] as $task) {
        $deadline = date("d.m.Y", strtotime($task["deadline"]));
        $message_text = $message_text."- {$task['title']} на {$deadline}.\n";
    }
    $message->text($message_text);
    $mailer = new Mailer($transport);
    $mailer->send($message);
}
