<?php
/**
 * Подсчитывает количество задач в проекте
 *
 * @param array $tasks_list список задач в виде массива
 * @param string $project_name название проекта
 *
 * @return number количество задач в проекте
 */
function count_tasks($tasks_list, $project_name) {
    $count = 0;
    foreach($tasks_list as $key => $value) {
        if ($value['category'] === $project_name) {
            $count++;
        }
    }
    return $count;
}  