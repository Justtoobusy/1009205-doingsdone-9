<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';
require 'init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $required_fields = ['email', 'password'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Заполните обязательное поле!';
        }
    }
    if (!empty($_POST['email'])) {
        $user = getDataOne($con, "SELECT * FROM users WHERE email = ?", [$_POST['email']]);
        if (empty($user)) {
            $errors['email'] = 'Пользователь с таким адресом электронной почты не найден.';
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail введён некорректно';
        }
    }
    if (!count($errors) && $user) {
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    }
    if (count($errors)) {
        $content = include_template('auth.php', [
            'errors' => $errors
        ]);
    } else {
        header("Location: /index.php");
    }
} else {
    if (isset($_SESSION['user'])) {
        sleep(5);
        header("Location: /index.php");
    } else {
        $content = include_template('auth.php', []);
    }
}
$layout_content = include_template('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout_content);
