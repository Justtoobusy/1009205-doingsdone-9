<?php
function task_count($tasks, $project)
{
    $num = 0;
    foreach ($tasks as $task) {
        if ($task['category_name'] === $project) {
            $num++;
        }
    }
    return $num;
}

function diff_hours($date)
{
    $hours_left = floor((strtotime($date) - time()) / 3600);
    return $hours_left;
}

function is_important($task)
{
    return diff_hours($task) <= 24;
}

function getDataAll($con, $sql, $data = [])
{
    $result = [];
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}

function getDataOne($con, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($res);

}


function convertFilterToMysql(string $filter = null): string
{
    switch ($filter) {
        case 'today':
            $date = date('Y-m-d', time());
            $str = "AND t.deadline ='{$date}'";
            break;
        case 'tomorrow':
            $date = date('Y-m-d', time() + 86400);
            $str = "AND t.deadline ='{$date}'";
            break;
        case 'overdue':
            $date = date('Y-m-d', time());
            $str = "AND t.deadline < '{$date}'";
            break;
        default:
            $str = '';
    }
    return $str;
}

function isAuthUser($is_auth)
{
    if (!$is_auth) {
        http_response_code(403);
        print include_template('guest.php');
        return false;
    }
    return true;
}
