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

CREATE INDEX task_title ON tasks(title);