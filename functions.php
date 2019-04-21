<?php
function task_count($tasks, $project)
{
    $num = 0;
    foreach ($tasks as $task) {
        if ($task['category'] === $project) {
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
