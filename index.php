<?php
require 'helpers.php';
require 'functions.php';
require 'init.php';
require_once 'vendor/autoload.php';

isAuthUser($is_auth);

$category_id = null;
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
}

$filter = null;
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}
$date_filter_sql = convertFilterToMysql($filter);

if (isset($_GET['show_completed'])) {
    $_SESSION['show_completed_tasks'] = $_GET['show_completed'];
}
$category_id_filter_sql = '';
$param = [
    $_SESSION['user']['id']
];
if ($category_id) {
    $param[] = $category_id;
    $category_id_filter_sql = 'AND c.id = ?';
}
$task_search_sql = '';
if (isset($_GET['task_search'])) {
    $search_prepared = trim($_GET['task_search']);
}
if (!empty($search_prepared)) {
    $task_search_sql = "AND MATCH (t.title) AGAINST (?)";
    $param[] = $search_prepared;
}
$sql = "SELECT t.*,c.title as category_name,date_format(t.deadline,\"%d.%m.%Y\") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE t.user_id = ? {$date_filter_sql} {$category_id_filter_sql} {$task_search_sql} ORDER BY t.deadline ASC";
$tasks = getDataAll($con, $sql, $param);
if (empty($tasks)) {
    http_response_code(404);
}
if (isset($_GET['check'])) {
    $chosen_task = getDataOne($con, 'SELECT * FROM tasks WHERE id = ?', [$_GET['task_id']]);
    $flag = 0;
    if ($chosen_task['is_done'] === 0) {
        $flag = 1;
    }
    $stmt = db_get_prepare_stmt($con, 'UPDATE tasks SET is_done = ? WHERE id = ?', [$flag, $chosen_task['id']]);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: /index.php");
    }
}
$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks t WHERE t.category_id = c.id AND t.user_id = ?) AS task_count , c.id, c.title FROM categories c WHERE c.user_id = ?  GROUP BY c.id ', [$_SESSION['user']['id'], $_SESSION['user']['id']]);
$index_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'category_id' => $category_id,
    'show_completed_tasks' => $_SESSION['show_completed_tasks'] ?? NULL
]);
print include_template('layout.php', [
    'content' => $index_content,
    'is_auth' => $is_auth,
    'user' => 'user',
    'task_search' => 'task_search'
]);
