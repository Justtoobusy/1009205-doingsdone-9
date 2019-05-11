--закидываем пользователей
INSERT INTO users (email,username,password)
VALUES ('harveyj@mail.ru','Harvey_Johnson','harveyj'),('alemu@mail.ru','Alemu','alemus'),('pelena66@mail.ru','Mother','qwerty');
--закидываем проекты
INSERT INTO categories
(title) VALUES ('Учеба'),('Входящие'),('Работа'),('Домашние дела'),('Авто');
--добавляем задачи
INSERT INTO tasks (is_done,title,deadline,user_id,category_id)
VALUES (0,'Собеседование в IT компании','2019-12-01','1','4'),(0,'Выполнить тестовое задание','2019-12-25','1','4'),
(1,'Сделать задание первого раздела','2019-12-22','1','1'),(0,'Встреча с другом','2019-04-20','1','3'),
(0,'Купить корм для кота',NULL,'2','5');
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
