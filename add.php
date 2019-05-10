<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';

$con = mysqli_connect('localhost', 'root', '', 'doingsdone');
mysqli_set_charset($con, 'utf8');
$sql = 'SELECT t.*,c.title as category_name,date_format(t.deadline,"%d.%m.%Y") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id ';
$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks WHERE category_id = c.id ) AS task_count ,c.id, c.title FROM categories c  GROUP BY c.id', []);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = $_POST['name'];
    $task_category = $_POST['project'];
    $task_deadline = $_POST['date'];
    $task_file = $_FILES['file'];

    $required_fields = ['Name' => $task_name,'Project' => $task_category];
    foreach ($required_fields as $field => $value) {
        if (!$value) {
            $errors[$field] = 'Заполните обязательное поле';
        }
    }
    if (in_array($task_category, $projects)) {
        $errors['Project'] = 'Выберите существующий проект';
    }
    if (($task_deadline)) {
        if (!is_date_valid($task_deadline) || (strtotime($task_deadline) < time())) {
            $errors['Deadline'] = 'Выберите верную дату';
        }
    }
    if (count($errors) == 0) {
        $file_url = null;
        if (isset($task_file)) {
            $file_name = $task_file['name'];
            $file_path = __DIR__ . '/uploads/';
            $file_url = '/uploads/' . $file_name;
            move_uploaded_file($task_file['tmp_name'], $file_path . $file_name);
        }
        $sql = 'INSERT INTO tasks (dt_add,user_id,title,category_id,deadline,file_attachement) 
    VALUES (NOW(),1,?,?,?,?)';
        $stmt = db_get_prepare_stmt($con, $sql, [$task_name, $task_category, $task_deadline, $file_url]);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /index.php");
        }
    }
}
print include_template('add.php', [
    'projects' => $projects,
    'errors' => $errors
]);
