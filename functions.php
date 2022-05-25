<?php
/**
 * Подсчитывает количество невыполненных задач в проекте
 *
 * @param mysqli $connect состояние подключения к БД
 * @param string $project_name название проекта
 * @param int $user_id идентификатор пользователя
 *
 * @return int количество задач в проекте
 */
function count_tasks(mysqli $connect, string $project_name, int $user_id)
{
    $project_name = mysqli_real_escape_string($connect, $project_name);

    $sql = "SELECT count(*) FROM tasks t JOIN projects p ON project_id = p.id WHERE p.title = '$project_name' AND p.user_id = $user_id AND status = 0";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $tasks_count = mysqli_fetch_assoc($result);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $tasks_count['count(*)'];
}

/**
 * Определяет считать ли задачу важной (до конца выплнения осталось меньше 24 часов) или нет
 *
 * @param string $task_date дата окончания задачи
 *
 * @return bool true если задача важная, false если нет
 */
function check_important($task_date)
{
    if ($task_date) {
        $actual_timestamp = time();
        $task_timestamp = strtotime($task_date);
        $remainder_in_seconds = $task_timestamp - $actual_timestamp;
        $remainder_in_hours = floor($remainder_in_seconds / 3600);
        return $remainder_in_hours < 24;
    }
}

/**
 * Получает из базы данных имя пользователя по его id
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $user_id идентификатор пользователя
 *
 * @return string имя пользователя
 */
function get_user_name(mysqli $connect, int $user_id)
{
    if ($user_id) {
        $sql = "SELECT name FROM users WHERE id = $user_id";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $user = mysqli_fetch_assoc($result);
        } else {
            $error = mysqli_error($connect);
            print("Ошибка подключения к БД: " . $error);
        }
        return $user['name'];
    }
}

/**
 * Получает из базы данных список проектов пользователя по его id
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $user_id идентификатор пользователя
 *
 * @return array ассоциативный массив с названиями проектов
 */
function get_user_projects(mysqli $connect, int $user_id)
{
    $sql = "SELECT id, title FROM projects WHERE user_id = $user_id";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $projects;
}

/**
 * Получает из базы данных список задач пользователя по его id
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $user_id идентификатор пользователя
 *
 * @return array ассоциативный массив с задачами
 */
function get_all_user_tasks(mysqli $connect, int $user_id)
{
    $sql = "SELECT status, t.id, t.title, deadline, filepath, p.title project FROM tasks t JOIN projects p ON project_id = p.id WHERE t.user_id = $user_id ORDER BY t.id DESC";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $tasks;
}

/**
 * Получает из базы данных список задач пользователя, относящихся к конкретному проекту, по id этого пользователя
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $project_id идентификатор проекта
 * @param int $user_id идентификатор пользователя
 *
 * @return array ассоциативный массив с задачами
 */
function get_user_tasks_by_project(mysqli $connect, int $project_id, int $user_id)
{
    $sql = "SELECT status, t.id, t.title, deadline, filepath, p.title project FROM tasks t JOIN projects p ON project_id = p.id WHERE t.user_id = $user_id AND p.id = $project_id ORDER BY t.id DESC";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $tasks;
}

/**
 * Возвращает значение поля из отправленной формы
 *
 * @param string $input_name имя поля
 *
 * @return string значение поля
 */
function get_post_value($input_name)
{
    return $_POST[$input_name] ?? "";
}

/**
 * Провряет заполнено ли поле в форме
 *
 * @param string $input_name имя поля
 *
 * @return string|null текст ошибки
 */
function is_filled($input_name)
{
    if (empty($_POST[$input_name])) {
        return "Это поле не может быть пустым! ";
    }
}

/**
 * Провряет существование проекта по его id, полученному из поля формы
 *
 * @param array $projects массив с id всех проектов пользователя
 * @param string $input_name имя поля
 *
 * @return string|null текст ошибки
 */
function is_project_exist($projects, $input_name)
{
    $project_exists = false;
    foreach ($projects as $project) {
        if ($project['id'] === $_POST[$input_name]) {
            $project_exists = true;
        }
    }
    if (!$project_exists) {
        return "Проект должен быть существующим! ";
    }
}

/**
 * Провряет поле даты в форме на соответствие формату и актуальность
 *
 * @param string $input_name имя поля
 *
 * @return string|null текст ошибки
 */
function is_correct_date($input_name)
{
    if (!empty($_POST[$input_name])) {
        if (!is_date_valid($_POST[$input_name])) {
            return "Введите дату в формате ГГГГ-ММ-ДД! ";
        }

        $task_date = $_POST[$input_name];
        $actual_date = date('Y-m-d');
        if ((strtotime($actual_date) - strtotime($task_date)) / 86400 > 0) {
            return "Дата не может быть в прошлом! ";
        }
    }
}

/**
 * Добавляет новую задачу в базу данных
 *
 * @param mysqli $connect состояние подключения к БД
 * @param sting $title - заголовок задачи
 * @param sting $filepath - путь к прикрепленному файлу
 * @param sting $deadline - дата окончания задачи
 * @param int $project_id - id проекта, к которому относится задача
 * @param int $user_id - id пользователя, который создал задачу
 *
 */
function add_new_task(mysqli $connect, string $title, ?string $filepath, ?string $deadline, int $project_id, int $user_id)
{
    $title = mysqli_real_escape_string($connect, $title);
    $filepath = mysqli_real_escape_string($connect, $filepath);
    $deadline = mysqli_real_escape_string($connect, $deadline);

    $sql = "INSERT INTO tasks SET title = '$title', project_id = '$project_id', user_id='$user_id'";
    if ($deadline) {
        $sql = $sql . ", deadline = '$deadline'";
    }
    if ($filepath) {
        $sql = $sql . ", filepath = '$filepath'";
    }
    $sql = $sql . ";";

    $result = mysqli_query($connect, $sql);
    if (!$result) {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
        exit();
    }
}

/**
 * Добавляет нового пользователя в базу данных
 *
 * @param mysqli $connect состояние подключения к БД
 * @param sting $email - электронная почта пользователя
 * @param sting $password - пароль пользователя
 * @param sting $name - имя пользователя
 *
 */
function add_new_user(mysqli $connect, string $email, string $password, string $name)
{
    $email = mysqli_real_escape_string($connect, $email);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $name = mysqli_real_escape_string($connect, $name);

    $sql = "INSERT INTO users SET email = '$email', password = '$password', name='$name';";
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
        exit();
    }
}

/**
 * Провряет email на валидность
 *
 * @param mysqli $connect состояние подключения к БД
 * @param string $email пользователя
 *
 * @return string|null текст ошибки
 */
function check_email_validity(mysqli $connect, string $email)
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email! ";
    }
}

/**
 * Провряет email на наличие в БД
 *
 * @param mysqli $connect состояние подключения к БД
 * @param string $email пользователя
 *
 * @return string|null текст ошибки
 */
function check_email_availability(mysqli $connect, string $email)
{
    $email = mysqli_real_escape_string($connect, $email);

    $sql = "SELECT email FROM users WHERE email = '$email';";
    $result = mysqli_query($connect, $sql);
    $email_in_base = mysqli_fetch_assoc($result);
    if ($email_in_base) {
        return "Пользователь с таким email уже зарегистрирован! ";
    }
}

/**
 * Сравнивает полученный от пользователя пароль с хэшем из БД
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $email email пользователя
 * @param int $password пароль пользователя
 *
 * @return bool true если пароль совпадает, false если нет
 */
function check_password(mysqli $connect, string $email, string $password)
{
    $email = mysqli_real_escape_string($connect, $email);

    $sql = "SELECT email FROM users WHERE email = '$email';";
    $result = mysqli_query($connect, $sql);
    $email_in_base = mysqli_fetch_assoc($result);
    if ($email_in_base) {
        $sql = "SELECT password FROM users WHERE email = '$email'";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $password_hash = mysqli_fetch_assoc($result);
        } else {
            $error = mysqli_error($connect);
            print("Ошибка подключения к БД: " . $error);
        }
        return password_verify($password, $password_hash['password']);
    }
}

/**
 * Получает из базы данных id пользователя по его email
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $email email пользователя
 *
 * @return int id пользователя
 */
function get_user_id(mysqli $connect, string $email)
{
    $email = mysqli_real_escape_string($connect, $email);

    $sql = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $user['id'];
}

/**
 * Получает из базы данных список задач пользователя, в названии которых есть хотя бы одно слово из поискового запроса
 *
 * @param mysqli $connect состояние подключения к БД
 * @param string $search_phrase поисковый запрос
 * @param int $user_id идентификатор пользователя
 *
 * @return array ассоциативный массив с задачами
 */
function get_user_tasks_by_search(mysqli $connect, string $search_phrase, int $user_id)
{
    $search_phrase = mysqli_real_escape_string($connect, $search_phrase);

    $sql = "SELECT * FROM tasks WHERE MATCH(title) AGAINST('$search_phrase') AND user_id = $user_id ORDER BY id DESC";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $tasks;
}

/**
 * Отмечает задачу выполненной или не выполненной
 *
 * @param mysqli $connect состояние подключения к БД
 * @param int $task_id иденитификатор задачи
 * @param int $task_status статус задачи, полученный из GET-параметра check
 * @param int $user_id идентификатор пользователя
 *
 * @return array ассоциативный массив с задачами
 */
function mark_task_completed(mysqli $connect, int $task_id, int $task_status, int $user_id)
{
    $sql = "UPDATE tasks SET status = '$task_status' WHERE id ='$task_id' AND user_id = '$user_id'";
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    header("Location: /index.php");
}

/**
 * Добавляет новый проект в базу данных
 *
 * @param mysqli $connect состояние подключения к БД
 * @param sting $title - название проекта
 * @param int $user_id - идентификатор пользователя, который создал проект
 *
 */
function add_new_project(mysqli $connect, string $title, int $user_id)
{
    $title = mysqli_real_escape_string($connect, $title);

    $sql = "INSERT INTO projects SET title = '$title', user_id='$user_id'";
    $result = mysqli_query($connect, $sql);
    if (!$result) {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
        exit();
    }
}

/**
 * Проверяет есть ли у пользователя проект с таким же названием
 *
 * @param mysqli $connect состояние подключения к БД
 * @param sting $title - название проекта
 * @param int $user_id - идентификатор пользователя, который создал проект
 *
 */
function is_unique_name(mysqli $connect, string $title, int $user_id)
{
    $title = mysqli_real_escape_string($connect, $title);

    $sql = "SELECT title FROM projects WHERE user_id = '$user_id' AND title = '$title';";
    $result = mysqli_query($connect, $sql);
    $project_in_base = mysqli_fetch_assoc($result);
    if ($project_in_base) {
        return "Проект с таким названием уже есть! ";
    }
}

/**
 * Получает список задач с заданным сроком окончания
 *
 * @param mysqli $connect состояние подключения к БД
 * @param string $task_deadline значение GET-параметра deadline
 * @param int $user_id идентификатор пользователя
 *
 * @return array ассоциативный массив с задачами
 */
function get_user_tasks_by_deadline(mysqli $connect, string $task_deadline, int $user_id)
{
    $task_deadline = mysqli_real_escape_string($connect, $task_deadline);

    $deadline = "";
    if ($task_deadline === "today") {
        $deadline = date("Y-m-d", strtotime('00:00:00'));
    } elseif ($task_deadline === "tomorrow") {
        $deadline = date("Y-m-d", strtotime('+1 day 00:00:00'));
    }
    if ($task_deadline === "today" || $task_deadline === "tomorrow") {
        $sql = "SELECT * FROM tasks WHERE deadline = '$deadline' AND user_id = $user_id ORDER BY id DESC";
    } elseif ($task_deadline === "overdue") {
        $today = date("Y-m-d", strtotime('00:00:00'));
        $sql = "SELECT * FROM tasks WHERE DATE(deadline) < '$today' AND status = 0 AND user_id = $user_id ORDER BY deadline DESC";
    } else {
        return [];
    }
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка подключения к БД: " . $error);
    }
    return $tasks;
}

/**
 * Получаем задачи запланированные на сегодня и список пользователей
 *
 * @param mysqli $connect состояние подключения к БД
 *
 * @return array ассоциативный массив с пользователями и их задачами на сегодня
 */
function get_users_today_tasks($connect)
{
    $today = date("Y-m-d", strtotime('00:00:00'));
    $sql = "SELECT u.id, u.email, u.name, t.title, t.deadline FROM users u JOIN tasks t ON t.user_id = u.id WHERE t.status = '0' AND t.deadline = '$today'";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $error = mysqli_error($connect);
        print("Ошибка MySQL" . $error);
    }
    return $tasks;
}
