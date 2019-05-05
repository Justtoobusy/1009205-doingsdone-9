<?php
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

$category_id = null;
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
}

require 'helpers.php';
require 'functions.php';
require 'data.php';

$con = mysqli_connect('localhost', 'root', '', 'doingsdone');
mysqli_set_charset($con, 'utf8');

$projects = getDataAll($con, 'SELECT id,title FROM categories', []);
if ($category_id) {
    $tasks = getDataAll($con, "SELECT t.*,c.title as category_name,date_format(t.deadline,\"%d.%m.%Y\") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE c.id = {$category_id}", []);

} else {
    $tasks = getDataAll($con, 'SELECT t.*,c.title as category_name,date_format(t.deadline,"%d.%m.%Y") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id ', []);
}
$tasks_counts = getDataAll($con, 'SELECT COUNT(id) AS task_count ,category_id FROM tasks GROUP BY category_id', []);
$tasks_count_projects = [];
foreach ($tasks_counts as $tasks_count) {
    $tasks_count_projects[$tasks_count['category_id']] = $tasks_count['task_count'];
}

foreach ($projects as &$project) {
    $params = $_GET;
    $params['category_id'] = $project['id'];
    $scriptname = pathinfo(__FILE__, PATHINFO_BASENAME);
    $query = http_build_query($params);
    $project['url'] = "/" . $scriptname . "?" . $query;
    $project['task_count'] = $tasks_count_projects[$project['id']] ?? 0;
    $project['is_active'] = $project['id'] == $category_id;
};
$index_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks
]);
if ($category_id && !$tasks) {
    http_response_code(404);
}
print include_template('layout.php', [
    'content' => $index_content,
    'title' => 'Дела в порядке'
]);


