CREATE DATABASE doingsdone
DEFAULT CHARACTER SET UTF8
DEFAULT COLLATE UTF8_GENERAL_CI;

USE doingsdone;

CREATE TABLE categories(
id INT AUTO_INCREMENT PRIMARY KEY,
title CHAR(64) NOT NULL UNIQUE);

CREATE TABLE users(
id INT AUTO_INCREMENT PRIMARY KEY,
reg_dt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
email CHAR(128) NOT NULL UNIQUE,
username CHAR(64) NOT NULL,
password TEXT NOT NULL);

CREATE TABLE tasks (
id INT AUTO_INCREMENT PRIMARY KEY,
dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
is_done INT DEFAULT '0',
title CHAR(128),
file_attachement CHAR(255),
deadline DATETIME,
user_id INT,
category_id INT);
