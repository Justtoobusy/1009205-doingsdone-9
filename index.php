<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
require 'helpers.php';
require 'functions.php';
require 'data.php';

$index_content = include_template('index.php', ['projects' => $projects, 'tasks' => $tasks]);

$layout_content = include_template('layout.php', ['content' => $index_content , 'title' => 'Дела в порядке']);

print($layout_content);

?>
