<?php
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once("vendor/autoload.php");
require_once("init.php");

$dsn = 'smtp://71ab17249e5d8d:38ef5b66ad2053@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
$transport = Transport::fromDsn($dsn);

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
    $message->from("keks@phpdemo.ru");
    $message->subject("Уведомление от сервиса «Дела в порядке»");
    $message_text = "Уважаемый, {$data['name']}.\n";
    if (count($data["tasks"]) === 1) {
        $message_text = $message_text."У вас запланирована задача:\n";
    } elseif (count($data["tasks"]) > 1) {
        $message_text = $message_text."У вас запланированы задачи:\n";
    }
    foreach ($data["tasks"] as $task) {
        $task["deadline"] = date("d.m.Y");
        $message_text = $message_text."- {$task['title']} на {$task['deadline']}.\n";
    }
    $message->text($message_text);
    $mailer = new Mailer($transport);
    $mailer->send($message);
}
