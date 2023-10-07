# Doings done
![PHP Version](https://img.shields.io/badge/php-%5E7.0-7A86B8)
![MySQL Version](https://img.shields.io/badge/mysql-%5E5.6-F29221)
![PHPUnit Version](https://img.shields.io/badge/phpunit-%5E7.5-3A97D0)

## О проекте

«Doings done» — это веб-сервис для ведения списка дел, работающий на чистом PHP и MySQL, без использования фреймворков. Проект начального уровня сложности, реализован с помощью простой методологии процедурного программирования.

Демонстрационная версия доступна по адресу https://doingsdone.sokoloff-rv.ru/. 

Для входа в **демо-аккаунт** используйте следующие данные:

- Логин: user@demo.ru
- Пароль: demopass

## Функциональность

Основные возможности, реализованные в проекте:

- Регистрация на сайте;
- Авторизация;
- Добавление новых проектов (категорий);
- Создание задач (каждая задача привязывается к проекту, может иметь дату окончания и вложение в виде файла);
- Подсчет количества задач в проектах (не считая завершенные задачи);
- Фильтрация и отображение задач по группам:
    - все задачи,
    - повестка дня,
    - задачи на завтра,
    - просроченные задачи;
- Отображение задачи как важной, если до её дедлайна осталось менее 24 часов;
- Поиск по задачам;
- Скрытие или отображение выполненных задач;
- Валидация всех форм;
- Возврат страницы с ошибкой 404, если пользователь пытается открыть страницу с несуществующим проектом;
- Отправка уведомлений о запланированных задачах на email пользователя.

## Обзор проекта

[![Видео](https://sokoloff-rv.ru/share/github/doingsdone.webp)](https://youtu.be/DAetbaQYWEI)

## Начало работы

Чтобы развернуть проект локально или на хостинге, выполните последовательно несколько действий:

1. Клонируйте репозиторий:

```bash
git clone https://github.com/sokoloff-rv/doingsdone.git doingsdone
```

2. Перейдите в директорию проекта:

```bash
cd doingsdone
```

3. Установите зависимости, выполнив команду:

```bash
composer install
```

4. Создайте базу данных для проекта, используя схему из файла `schema.sql`:

```sql
CREATE DATABASE doingsdone
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    register_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    password VARCHAR(100) NOT NULL
);

CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    user_id INT NOT NULL
);

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status BOOL NOT NULL DEFAULT '0',
    title VARCHAR(255) NOT NULL,
    filepath VARCHAR(255),
    deadline DATETIME,
    project_id INT NOT NULL,
    user_id INT NOT NULL
);

CREATE FULLTEXT INDEX task_title_search ON tasks(title);
```

5. Настройте подключение к базе данных, создав в корне проекта файл `database.php` и указав параметры своего окружения. Например, это может выглядеть так:

```php
<?php

return [
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    'name' => 'doingsdone'
];
```

## Техническое задание

[Посмотреть техническое задание проекта](https://sokoloff-rv.notion.site/Doings-done-f0e0b4c20066446fb640d4b03dfbd57e?pvs=4)
