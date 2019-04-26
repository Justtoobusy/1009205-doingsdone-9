<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

require 'helpers.php';
require 'functions.php';
require 'data.php';

$con = mysqli_connect('localhost', 'root', '', 'doingsdone');
mysqli_set_charset($con, 'utf8');

$projects = getDataAll($con, 'SELECT id,title FROM categories', []);
$tasks = getDataAll($con, 'SELECT t.*,c.title as category_name,date_format(t.deadline,"%d.%m.%Y") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id ', []);
$index_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks
]);

print include_template('layout.php', [
    'content' => $index_content,
    'title' => 'Дела в порядке'
]);


