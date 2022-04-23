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

/**
 * Определяет считать ли задачу важной (до конца выплнения осталось меньше 24 часов) или нет
 *
 * @param string $task_date дата окончания задачи
 *
 * @return bool true если задача важная, false если нет
 */
function check_important($task_date) {
    if ($task_date) {
        $actual_timestamp = time();
        $task_timestamp = strtotime($task_date);
        $remainder_in_seconds = $task_timestamp - $actual_timestamp;
        $remainder_in_hours = floor($remainder_in_seconds / 3600);
        $remainder_in_hours < 24 ? $is_important = true : $is_important = false;
        return $is_important;
    }
}  