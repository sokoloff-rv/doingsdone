<?php
function count_tasks($tasks_list, $project_name) {
    $count = 0;
    foreach($tasks_list as $key => $val) {
        if ($val['category'] === $project_name) {
            $count++;
        }
    }
    return $count;
}  