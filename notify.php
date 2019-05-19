<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';
require 'init.php';
require_once 'vendor/autoload.php';

$transport = new Swift_SmtpTransport("phpdemo.ru", 25);
$transport->setUsername("keks@phpdemo.ru");
$transport->setPassword("htmlacademy");

$mailer = new Swift_Mailer($transport);
$current_date_sql = date('Y-m-d', time());
$message = new Swift_Message();

$users = getDataAll($con, 'SELECT * FROM users');
$message_content = '';
$recipients = [];

foreach ($users as $user) {
    $tasks = getDataAll($con, 'SELECT title,date_format(deadline,"%d.%m.%Y") AS deadline_formatted FROM tasks WHERE is_done = 0 AND deadline = ? AND user_id =?', [$current_date_sql, $user['id']]);
    if (!empty($tasks)) {
        $recipients = $user['email'];
        $today_tasks = [];
        foreach ($tasks as $task) {
            $today_tasks[] = $task['title'];
            $today_tasks = array_values($today_tasks);
        }
        $phrase = '';
        if (count($today_tasks) > 1){
            $phrase = ". У вас запланированы задачи: ";
        } else {
            $phrase = ". У вас запланирована задача: ";
        }
        $message_content = "Уважаемый " . $user['username'] . $phrase . implode(' , ', $today_tasks) . " на " . date('d.m.Y', time());
    }
    $message->setSubject("Уведомление от сервиса «Дела в порядке»");
    $message->setFrom('keks@phpdemo.ru');
    $message->setBcc($recipients);
    $message->setBody($message_content, 'text/html');
    $result = $mailer->send($message);
}
if ($result) {
    print("Рассылка успешно отправлена");
} else {
    print("Не удалось отправить рассылку");
}

