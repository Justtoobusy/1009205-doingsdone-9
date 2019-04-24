--закидываем пользователей
INSERT INTO users 
SET email = 'harveyj@mail.ru', username = 'Harvey_Johnson', PASSWORD = 'harveyj';
INSERT INTO users 
SET email = 'alemu@mail.ru', username = 'Alemu', PASSWORD = 'alemus' ;
INSERT INTO users 
SET email = 'pelena66@mail.ru', username = 'Mother', PASSWORD = 'qwerty' ;
--закидываем проекты
INSERT INTO categories
(title) VALUES ('Учеба','Входящие','Работа','Домашние дела','Авто');
--добавляем задачи
INSERT INTO tasks
SET is_done = 0, title = 'Собеседование в IT компании', deadline = '2019-12-01', user_id ='1', category_id = '4';
INSERT INTO tasks
SET is_done = 0, title = 'Выполнить тестовое задание', deadline = '2019-12-25', user_id ='1', category_id = '4';
INSERT INTO tasks
SET is_done = 1, title = 'Сделать задание первого раздела', deadline = '2019-12-22', user_id ='1', category_id = '1';
INSERT INTO tasks
SET is_done = 0, title = 'Встреча с другом', deadline = '2019-04-20', user_id ='1', category_id = '3';
INSERT INTO tasks
SET is_done = 0, title = 'Купить корм для кота', deadline = NULL, user_id ='2', category_id = '5';
--пометить задачу как выполненную
UPDATE tasks SET is_done = 1
WHERE id = '4';
--пометить задачу как выполненную
UPDATE tasks SET title = 'Купить корм для хомяка'
WHERE id = '7';
--получить список из всех задач для одного проекта
SELECT * FROM tasks
WHERE category_id = 4;
--
SELECT c.id,c.title,COUNT(c.id) AS count FROM categories AS c
LEFT JOIN tasks AS t
ON c.id = t.category_id
WHERE t.user_id = 1
GROUP BY c.id;
