<?php

/**
 * Считает количество задач в каждом из проектов
 *
 * @param array $project  массив с проектами
 * @param array $tasks  массив с задачами
 *
 * @return int количество задач в проекте
 */
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

/**
 * Считает разницу в часах между дэдлайном по задаче и текущим временем
 *
 * @param string $date дэдлайн по задаче
 *
 * @return int количество часов, оставшихся до наступления дэдлайна по задаче
 */
function diff_hours($date)
{
    $hours_left = floor((strtotime($date) - time()) / 3600);
    return $hours_left;
}

/**
 * Определяет задачу как важную, если до дэдлайна по ней осталось менее 24 часов
 *
 * @param string $task задача
 *
 * @return bool true если до дэдлайна по задаче осталось меньше либо равно 24 часов, иначе false
 */
function is_important($task)
{
    return diff_hours($task) <= 24;
}

/**
 * Получает двумерный массив с данными на основе готового SQL запроса и переданных данных
 * @param mysqli $con Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return array двумерный массив с данными, полученными из БД
 */
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

/**
 * Получает ассоциативный одномерный массив с данными на основе готового SQL запроса и переданных данных
 * @param mysqli $con Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return array ассоциативный одномерный массив с данными, полученными из БД
 */
function getDataOne($con, $sql, $data = [])
{
    $stmt = db_get_prepare_stmt($con, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($res);

}

/**
 * Подготавливает SQL выражение для отображения списка задач, запланированных на определенный временной отрезок(сегодня, завтра, просроченные, все)
 *
 * @param string $filter выбранный временной отрезок
 *
 * @return string Подготовленное SQL выражение для дальнейшего запроса списка задач за этот промежуток времени
 */
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

/**
 * Проверяет наличие данных об авторизации ползьователя(авторизован ли пользователь на сайте)
 *
 * @param bool $is_auth данные об авторизации
 *
 * @return bool true если пользователь авторизован, иначе false
 */
function isAuthUser($is_auth)
{
    if (!$is_auth) {
        http_response_code(403);
        print include_template('guest.php');
        return false;
    }
    return true;
}
