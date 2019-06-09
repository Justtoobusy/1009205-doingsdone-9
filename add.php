<?php
require 'helpers.php';
require 'functions.php';
require 'init.php';
require_once 'vendor/autoload.php';

isAuthUser($is_auth);
$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks t WHERE t.category_id = c.id AND t.user_id = ?) AS task_count , c.id, c.title FROM categories c WHERE c.user_id = ?  GROUP BY c.id ', [$_SESSION['user']['id'], $_SESSION['user']['id']]);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['project'])) {
    $required_fields = ['name', 'project'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || !$_POST[$field]) {
            $errors[$field] = 'Заполните обязательное поле';
        }
    }
    if (empty(getDataOne($con, 'SELECT * FROM categories WHERE user_id =? AND id = ?', [$_SESSION['user']['id'], $_POST['project']]))) {
        $errors['project'] = 'Выберите существующий проект';
    }
    if (!empty($_POST['date']) && (!is_date_valid($_POST['date']) || strtotime($_POST['date']) < time())) {
        $errors['deadline'] = 'Выберите верную дату';
    }
    if (count($errors) === 0) {
        $file_name = null;
        if (isset($_FILES['file'])) {
            $file_name = $_FILES['file']['name'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
        }
        $task_deadline = $_POST['date'];
        if ($_POST['date'] === '') {
            $task_deadline = null;
        }
        $stmt = db_get_prepare_stmt($con, 'INSERT INTO tasks (dt_add,user_id,title,category_id,deadline,file_attachement)
    VALUES (NOW(),?,?,?,?,?)', [
            $_SESSION['user']['id'],
            $_POST['name'],
            $_POST['project'],
            $task_deadline,
            $file_name
        ]);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /index.php");
        }
    }
}

print include_template('add.php', [
    'projects' => $projects,
    'errors' => $errors,
    'is_auth' => $is_auth
]);
