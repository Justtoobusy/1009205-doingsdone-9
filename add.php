<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';
require 'init.php';

$sql = 'SELECT t.*,c.title as category_name,date_format(t.deadline,"%d.%m.%Y") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id ';
$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks WHERE category_id = c.id ) AS task_count ,c.id, c.title FROM categories c  GROUP BY c.id', []);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $required_fields = ['name', 'project'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $errors[$field] = 'Заполните обязательное поле';
        }
    }
    if (in_array($_POST['project'], $projects)) {
        $errors['project'] = 'Выберите существующий проект';
    }
    if (!empty($_POST['date']) && (!is_date_valid($_POST['date']) || strtotime($_POST['date']) < time())) {
        $errors['Deadline'] = 'Выберите верную дату';
    }
    if (count($errors) == 0) {
        $file_name = null;
        if (isset($_FILES['file'])) {
            $file_name = $_FILES['file']['name'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
        }
        $task_deadline = $_POST['date'];
        if ($_POST['date'] == ''){
            $task_deadline = null;
        }
        $stmt = db_get_prepare_stmt($con, 'INSERT INTO tasks (dt_add,user_id,title,category_id,deadline,file_attachement)
    VALUES (NOW(),1,?,?,?,?)', [
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
    'errors' => $errors
]);
