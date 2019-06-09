<?php

require 'helpers.php';
require 'functions.php';
require 'init.php';
require_once 'vendor/autoload.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['password'])) {
    $required_fields = ['email', 'password', 'name'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Заполните обязательное поле!';
        }
    }
    if (!empty($_POST['email'])) {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail введён некорректно';
        }
        $existing_users = getDataOne($con, 'SELECT COUNT(*) as exist FROM users WHERE email = ?', [$_POST['email']]);
        if ($existing_users['exist'] !== 0) {
            $errors['email'] = 'Пользователь с таким email уже зарегистрирован';
        }
    }
    if (count($errors) === 0) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = db_get_prepare_stmt($con, 'INSERT INTO users (reg_dt,email,password,username) VALUES (NOW(),?,?,?)',
            [
                $_POST['email'],
                $password,
                $_POST['name']
            ]);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /index.php");
        }
    }
}

print include_template('register.php', [
    'errors' => $errors
]);
