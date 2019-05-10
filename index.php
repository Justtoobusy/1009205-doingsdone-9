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


$sql = 'SELECT t.*,c.title as category_name,date_format(t.deadline,"%d.%m.%Y") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id ';
$param = [];
if ($category_id) {
    $sql = "SELECT t.*,c.title as category_name,date_format(t.deadline,\"%d.%m.%Y\") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE c.id = ?";
$param = [$category_id];
}
$tasks = getDataAll($con, $sql, $param);
if (empty($tasks)) {
    http_response_code(404);
}
$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks WHERE category_id = c.id ) AS task_count ,c.id, c.title FROM categories c  GROUP BY c.id', []);

$index_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'category_id' => $category_id
]);

print include_template('layout.php', [
    'content' => $index_content,
    'title' => 'Дела в порядке'
]);

