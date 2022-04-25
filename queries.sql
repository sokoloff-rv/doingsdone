USE doingsdone;

/* Записываю в БД список пользователей */
INSERT INTO users (name, email, password) VALUES ('Роман', 'sokoloff.rv@gmail.com', 'qweasd123'), ('Джон Доу', 'johndoe@jpod.com', 'Coupland30121961'), ('Иванов Иван Иванович', 'ivan1990@notmail.ru', 'ivan1990');

/* Записываю в БД список проектов */
INSERT INTO projects (title, user_id) VALUES ('Входящие', 1), ('Учеба', 1), ('Работа', 1), ('Домашние дела', 1), ('Авто', 1);

/* Записываю в БД список задач */
INSERT INTO tasks (title, user_id, project_id, deadline, status) VALUES ('Собеседование в IT компании', 1, 3, '2022-04-23 00:00:00', 0), ('Выполнить тестовое задание', 1, 3, '2022-05-25 00:00:00', 0), ('Сделать задание первого раздела', 1, 2, '2022-05-21 00:00:00', 1), ('Встреча с другом', 1, 1, '2022-05-22 00:00:00', 0), ('Купить корм для кота', 1, 4, NULL, 0), ('Заказать пиццу', 1, 4, NULL, 0);

/* Получаю список из всех проектов для одного пользователя */
SELECT * FROM projects WHERE user_id = 1;

/* Получаю список из всех задач для одного проекта */
SELECT * FROM tasks WHERE project_id = 1;

/* Помечаю задачу выполненной */
UPDATE tasks SET status = 1 WHERE id = 1;

/* Обновляю название задачи по её идентификатору */
UPDATE tasks SET title = 'Новое название задачи' WHERE id = 1;