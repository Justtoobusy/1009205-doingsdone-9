<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';
require 'init.php';
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$category_id = null;
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
}

$sql = 'SELECT t.*,c.title as category_name,date_format(t.deadline,"%d.%m.%Y") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE t.user_id = ?';
$param = [$_SESSION['user']['id']];
if ($category_id) {
    $sql = "SELECT t.*,c.title as category_name,date_format(t.deadline,\"%d.%m.%Y\") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE c.id = ? AND t.user_id = ?";
    $param = [$category_id, $_SESSION['user']['id']];
}
$tasks = getDataAll($con, $sql, $param);
if (empty($tasks)) {
    http_response_code(404);
}
$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks t WHERE t.category_id = c.id AND t.user_id = ?) AS task_count , c.id, c.title FROM categories c  GROUP BY c.id ', [$_SESSION['user']['id']]);

$index_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'category_id' => $category_id
]);

if ($is_auth) {
    print include_template('layout.php', [
        'content' => $index_content,
        'title' => 'Дела в порядке',
        'is_auth' => $is_auth
    ]);
} else {
    header("Location: /guest.php");
}
