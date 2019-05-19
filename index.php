<?php
require 'helpers.php';
require 'functions.php';
require 'data.php';
require 'init.php';

$category_id = null;
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
}

$filter = null;
if (isset($_GET['filter'])) {
    $filter = $_GET['filter'];
}
$date_filter_sql =convertFilterToMysql($filter);
$show_completed_tasks = null;
if (isset($_GET['show_completed'])) {
    $show_completed_tasks = $_GET['show_completed'];
}
$category_id_filter_sql = '';
$param = [
    $_SESSION['user']['id']
];
if ($category_id) {
    $param[] = $category_id;
    $category_id_filter_sql = 'AND c.id = ?';
}


$sql = "SELECT t.*,c.title as category_name,date_format(t.deadline,\"%d.%m.%Y\") as deadline FROM tasks t LEFT JOIN categories c ON t.category_id = c.id WHERE t.user_id = ? {$date_filter_sql} {$category_id_filter_sql} ORDER BY t.deadline ASC";
$tasks = getDataAll($con, $sql, $param);
if (empty($tasks)) {
    http_response_code(404);
}
if (isset($_GET['check'])) {
    $chosen_task = getDataOne($con, 'SELECT * FROM tasks WHERE id = ?', [$_GET['task_id']]);
    if ($chosen_task['is_done'] == 0) {
        $stmt = db_get_prepare_stmt($con, 'UPDATE tasks SET is_done = 1 WHERE id = ?', [$chosen_task['id']]);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /index.php");
        }
    } else {
        $stmt = db_get_prepare_stmt($con, 'UPDATE tasks SET is_done = 0 WHERE id = ?', [$chosen_task['id']]);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: /index.php");
        }
    }
}

$projects = getDataAll($con, 'SELECT (SELECT COUNT(*) FROM tasks t WHERE t.category_id = c.id AND t.user_id = ?) AS task_count , c.id, c.title FROM categories c WHERE c.user_id = ?  GROUP BY c.id ', [$_SESSION['user']['id'], $_SESSION['user']['id']]);

$index_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'category_id' => $category_id,
    'show_completed_tasks' => $show_completed_tasks
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
