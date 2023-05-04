INSERT INTO `categories`(`id`, `character_code`, `name_category`) VALUES (null,'boards','Доски и лыжи');
INSERT INTO `categories`(`id`, `character_code`, `name_category`) VALUES (null,'attachment','Крепления');
INSERT INTO `categories`(`id`, `character_code`, `name_category`) VALUES (null,'boots','Ботинки');
INSERT INTO `categories`(`id`, `character_code`, `name_category`) VALUES (null,'clothing','Одежда');
INSERT INTO `categories`(`id`, `character_code`, `name_category`) VALUES (null,'tools','Инструменты');
INSERT INTO `categories`(`id`, `character_code`, `name_category`) VALUES (null,'other','Разное');

INSERT INTO `users`(`email`, `user_name`, `user_password`, `contacts`)
VALUES ('biba@mail.com','Biba','123456','88005553535');
INSERT INTO `users`(`email`, `user_name`, `user_password`, `contacts`)
VALUES ('boba@mail.com','Boba','123456','88005553536');

INSERT INTO `lots`(`title`, `lot_description`, `img`, `start_price`, `date_finish`, `step`, `user_id`, `category_id`)
VALUES ('2014 Rossignol District Snowboard','Мягкий универсальный сноуборд для начинающих','img/lot-1.jpg', 10990, '2023-05-10', 500, 1, 1),
       ('DC Ply Mens 2016/2017 Snowboard','Мягкий универсальный сноуборд для начинающих','img/lot-2.jpg', 15999, '2023-05-15', 1000, 2, 1),
       ('Крепления Union Contact Pro 2015 года размер L/XL','Крипления для фрирайда средней жесткости','img/lot-3.jpg', 8000, '2023-05-20', 500, 1, 2),
       ('Ботинки для сноуборда DC Mutiny Charocal','Универсальные ботинки средней жесткости','img/lot-4.jpg', 10999, '2023-05-25', 700, 2, 3),
       ('Куртка для сноуборда DC Mutiny Charocal','Куртка с мембранной 20000\20000','img/lot-5.jpg', 7500, '2023-05-05', 300, 1, 4),
       ('Маска Oakley Canopy','Мужская маска среднего размера cat4','img/lot-6.jpg', 5400, '2023-05-20', 200, 2, 6);

INSERT INTO `bets`(`price_bet`, `user_id`, `lot_id`) VALUES (7000, 1, 1);
INSERT INTO `bets`(`price_bet`, `user_id`, `lot_id`) VALUES (10000, 1, 2);

/*все категории*/
SELECT name_category AS 'Категория'
FROM categories;

/*открытые лоты с ценой, названием, категорией и ссылкой*/
SELECT title AS 'Название', start_price AS 'Начальная цена', img AS 'Изображение', name_category AS 'Категория'
FROM lots JOIN categories ON lots.category_id=categories.id;

/*Показываем лот по ID и получаем навзание категории*/
SELECT lots.id, date_creation, title, lot_description, img, start_price, date_finish, step, name_category
FROM lots JOIN categories c on lots.category_id = c.id
WHERE lots.id = 4;

/*обновление названия лота*/
UPDATE lots SET title = 'Супер ботнки'
WHERE id = 4;

/*получаем список ставок по id с сотрировкой по дате*/
SELECT date_bet, price_bet, title, user_name FROM bets
JOIN lots l on l.id = bets.lot_id
JOIN users u on u.id = bets.user_id
WHERE l.id = 1
ORDER BY date_bet DESC;
