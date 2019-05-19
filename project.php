<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';
require 'init.php';

$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks t WHERE t.category_id = c.id AND t.user_id = ?) AS task_count , c.id, c.title FROM categories c WHERE c.user_id = ?  GROUP BY c.id ', [$_SESSION['user']['id'], $_SESSION['user']['id']]);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['name'])) {
        $errors['name'] = 'Заполните обязательное поле';
    }
    $existing_projects = getDataOne($con, 'SELECT COUNT(*) as already_added FROM categories WHERE title = ?', [$_POST['name']]);
    if ($existing_projects['already_added'] !== 0) {
        $errors['name'] = 'Такой проект уже существует';
    }
    if (count($errors) == 0) {
        $stmt = db_get_prepare_stmt($con, 'INSERT INTO categories (title,user_id)
    VALUES (?,?)', [
            $_POST['name'],
            $_SESSION['user']['id']
        ]);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /index.php");
        }
    }
}
print include_template('project.php', [
    'projects' => $projects,
    'errors' => $errors,
    'is_auth' => $is_auth
]);
