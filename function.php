<?php

/**
 * Приводит число к денежному формату
 * @param int $number
 * @return string
 */
function price(int $number): string
{
    if (ceil($number) < 1000) {
        return $number;
    }

    return number_format($number, 0, ".", " ") . " ₽";
}

/**
 * @param string $date
 * @return array|string[]
 * @throws Exception
 * Сравнивает даты и возвращает разницу в часах и минутах
 */
function get_dt_range(string $date): array
{
    if ((new DateTime() >= new DateTime($date))) {
        return ["00", "00"];
    }
    $data_diff = (new DateTime())->diff(new DateTime($date))->format('%d-%H-%I');
    $result_date = explode('-', $data_diff);
    $hours = str_pad($result_date[0] * 24 + $result_date[1], 2, "0", STR_PAD_LEFT);
    $minutes = str_pad(intval($result_date[2]), 2, "0", STR_PAD_LEFT);

    return array($hours, $minutes);
}

/**
 * @param mysqli_result $query
 * @return array
 * В зависимости от запроса возвращает обыный или двумерный массив
 */
function get_array_or_2dArray(mysqli_result $query): array
{
    $num_row = mysqli_num_rows($query);
    if ($num_row === 1) {
        $res_array = mysqli_fetch_assoc($query);
    } else {
        $res_array = mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    return $res_array;
}

/**
 * @param $DB_connect mysqli
 * @return array|string
 * Возвращает массив категорий
 */
function get_category_query(mysqli $DB_connect): array|string
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $cat_query = $DB_connect->query("SELECT * FROM `categories`");
        if ($cat_query) {
            return $cat_query->fetch_all(MYSQLI_ASSOC);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param $DB_connect mysqli
 * @return array|string
 * Возвращает массив лотов
 */
function get_lots_query(mysqli $DB_connect): array|string
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $cat_query = $DB_connect->query(
            "
SELECT l.id, l.img, c.name_category, l.title, l.start_price, l.date_finish 
FROM lots l JOIN categories c ON l.category_id=c.id 
WHERE l.date_finish > NOW() 
ORDER BY l.date_creation DESC
"
        );
        if ($cat_query) {
            return $cat_query->fetch_all(MYSQLI_ASSOC);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param $DB_connect mysqli
 * @param int $id
 * @return array|string
 * Возвращает лоты выбранного пользователя
 */
function get_lot_query(mysqli $DB_connect, int $id): array|string
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $cat_query = $DB_connect->query(
            "
SELECT l.id, l.img, c.name_category, l.step, l.title, l.start_price, l.date_finish, l.lot_description
FROM lots l 
JOIN categories c ON l.category_id=c.id 
WHERE l.id = $id;
"
        );
        if ($cat_query) {
            return get_array_or_2dArray($cat_query);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param int $id
 * @param array $allow_list
 * @return string|null
 * Валидирует поле для категории
 */
function category_valid(int $id, array $allow_list): ?string
{
    if (!in_array($id, $allow_list)) {
        return "Указана не существующая категория";
    }
    return null;
}

/**
 * @param string $num
 * @return string|void|null
 * Валидирует моле номера
 */
function number_valid(string $num)
{
    if (!empty(intval($num))) {
        if (is_int($num) && $num > 0) {
            return null;
        }
    } else {
        return "Содержимое поля должно быть целым числом больше нуля";
    }
}

/**
 * @param string $date
 * @return string|void
 * @throws Exception
 * Валидирует дату
 */
function date_valid(string $date)
{
    if (is_date_valid($date)) {
        $data_diff = (new DateTime())->diff(new DateTime($date))->format('%d');

        if ($data_diff < 1) {
            return "Дата должна быть больше текущей не менее чем на один день";
        }
    } else {
        return "Содержимое поля 'дата завершения' должно быть датой в формате 'ГГГГ-ММ-ДД'";
    }
}

/**
 * @param $email
 * @param int|array $existing_email_list
 * @return string|null
 * Валидирует емайл
 */
function valid_email($email, int|array $existing_email_list = 0): ?string
{
    if ($existing_email_list !== 0) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (in_array($email, $existing_email_list)) {
                return "Пользовательн с таким email уже существует";
            } else {
                return null;
            }
        }
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    } else {
        return "E-mail должен быть корректным";
    }
}

/**
 * @param string $name
 * @param array $existing_name_list
 * @return string|null
 * Валидирует имя пользователя
 */
function valid_name(string $name, array $existing_name_list): ?string
{
    if (in_array($name, $existing_name_list)) {
        return "Пользовательн с таким именем уже существует";
    } else {
        return null;
    }
}

/**
 * @param $DB_connect mysqli
 * @return array|mixed
 * Возвращает массив пользователей
 */
function user_query(mysqli $DB_connect): mixed
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $email_query = $DB_connect->query(
            "
SELECT *
FROM users;
"
        );
        if ($email_query) {
            return get_array_or_2dArray($email_query);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param array $parameter_array
 * @param array $required
 * @param array $rules
 * @return array
 * Валидирует параметр запроса по заданным правилам.
 */
function field_validation(array $parameter_array, array $required, array $rules): array
{
    $errors = [];
    foreach ($parameter_array as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }
        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Поле $field нужно заполнить";
        }
    }
    return $errors;
}

/**
 * @param string $value
 * @param int $min
 * @param int $max
 * @return string|void
 * Валидирует длинну занчения поля
 */
function length_valid(string $value, int $min, int $max)
{
    $len = strlen($value);
    if ($len < $min || $len > $max) {
        return "Значение должно быть от $min до $max симовлов.";
    }
}

/**
 * @param $conn mysqli
 * @param string $words
 * @return mixed|string
 * Возвращает количество лотов
 */
function get_count_lots(mysqli $conn, string $words): mixed
{
    $sql = "SELECT COUNT(*) as cnt FROM lots WHERE MATCH(title, lot_description) AGAINST (?);";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $words);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!empty($res)) {
        return $res->fetch_assoc()['cnt'];
    }
    return $conn->error;
}

/**
 * @param $conn mysqli
 * @param string $words
 * @param int $limit
 * @param int $offset
 * Возвращает массив лотов соответствующих поиску
 */
function get_found_lots(mysqli $conn, string $words, int $limit, int $offset): array|string
{
    $sql = "SELECT l.id, l.title, l.start_price, l.img, l.date_finish, c.name_category 
    FROM lots l
    JOIN categories c 
    ON l.category_id = c.id
    WHERE (MATCH(title, lot_description) AGAINST(?) OR name_category LIKE (?)) AND l.date_finish >= NOW() ORDER BY l.date_creation DESC LIMIT $limit OFFSET $offset
    ;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $words, $words);
    $stmt->execute();
    $res = $stmt->get_result();

    if (!empty($res)) {
        return mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $conn->error;
}

/**
 * @param string $price
 * @param int $last_bet
 * @return string|null
 * Валидирует ставку
 */
function price_bet_valid(string $price, int $last_bet): ?string
{
    if (!empty($price) > 0) {
        if ($price > $last_bet) {
            return null;
        }
    }
    return "Содержимое поля должно быть целым числом больше нуля и быть больше чем последняя ставка";
}

/**
 * @param $DB_connect mysqli
 * @param int $id
 * @return array|string
 * Возвращает массив лотов со ставками пользователя
 */
function get_bet_query(mysqli $DB_connect, int $id): array|string
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $bets_query = $DB_connect->query(
            "
SELECT DATE_FORMAT(b.date_bet, '%d.%m.%y в %H:%i') as date_bet, b.price_bet, u.user_name
FROM bets b 
JOIN users u ON b.user_id = u.id
WHERE lot_id = $id
ORDER BY b.date_bet DESC LIMIT 10
;"
        );
        if ($bets_query) {
            return mysqli_fetch_all($bets_query, MYSQLI_ASSOC);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param $DB_connect mysqli
 * @return array|mixed
 * Возвращает массив лотов без победителя
 */
function get_lots_wo_winners(mysqli $DB_connect): mixed
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $lots_query = $DB_connect->query(
            "
SELECT id, title
FROM lots
WHERE date_finish <= NOW() AND winner_id IS NULL
;"
        );
        if ($lots_query) {
            return mysqli_fetch_all($lots_query, MYSQLI_ASSOC);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param $DB_connect mysqli
 * @param int $id
 * @return array|mixed
 * Возвращает массив последних ставок по лотам
 */
function get_last_bet(mysqli $DB_connect, int $id)
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $bet_query = $DB_connect->query(
            "
SELECT b.lot_id, b.user_id, MAX(price_bet) as max, u.email, u.user_name, l.title
FROM bets b
JOIN users u ON user_id = u.id
JOIN lots l ON lot_id = l.id
WHERE lot_id = $id
GROUP BY lot_id, user_id 
ORDER BY max DESC LIMIT 1
;"
        );
        if ($bet_query) {
            return mysqli_fetch_all($bet_query, MYSQLI_ASSOC);
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param $DB_connect mysqli
 * @param int $lot_id
 * @param int $user_id
 * @return mixed|null
 * Обнавляет БД, добавляет ИД победителя.
 */
function add_winners(mysqli $DB_connect, int $lot_id, int $user_id): mixed
{
    if (!$DB_connect) {
        return $DB_connect->connect_error;
    } else {
        $winner_query =
            "
UPDATE lots
SET winner_id = $user_id
WHERE id = $lot_id
;";
        if ($DB_connect->query($winner_query) === true) {
            return null;
        } else {
            return $DB_connect->error;
        }
    }
}

/**
 * @param $DB_connect mysqli
 * @param int $id
 * @return string|array|null
 */
function get_user_bet(mysqli $DB_connect, int $id): string|array|null
{
    if ($DB_connect) {
        /*$sql = "
SELECT DATE_FORMAT(b.date_bet, '%d.%m.%y в %H:%i') AS date_bet, b.price_bet, 
l.title, l.lot_description, l.img, l.date_finish, l.id, c.name_category, l.winner_id, b.user_id
, (SELECT contacts FROM users u JOIN lots l ON u.id = l.user_id WHERE l.winner_id = $id LIMIT 1) as contacts
FROM (SELECT MAX(date_bet) as date_bet, MAX(price_bet) as price_bet, user_id, lot_id FROM bets WHERE user_id = $id GROUP BY lot_id) b
JOIN lots l ON b.lot_id = l.id
JOIN users u ON b.user_id = u.id
JOIN categories c ON l.category_id = c.id
WHERE b.user_id = $id
ORDER BY b.date_bet DESC
        ";*/
        $sql = "
SELECT DATE_FORMAT(b.date_bet, '%d.%m.%y в %H:%i') AS date_bet, b.price_bet, 
l.title, l.lot_description, l.img, l.date_finish, l.id, c.name_category, l.winner_id, b.user_id
, x.contacts
FROM (SELECT MAX(date_bet) as date_bet, MAX(price_bet) as price_bet, user_id, lot_id FROM bets WHERE user_id = $id GROUP BY lot_id) b
JOIN lots l ON b.lot_id = l.id
JOIN users u ON b.user_id = u.id
JOIN categories c ON l.category_id = c.id
LEFT JOIN (SELECT contacts, l.id FROM users u JOIN lots l ON u.id = l.user_id WHERE l.winner_id = $id) AS x ON x.id = b.lot_id
WHERE b.user_id = $id
ORDER BY b.date_bet DESC
        ";
        $res = $DB_connect->query($sql);
        if ($res) {
            return $res->fetch_all(MYSQLI_ASSOC);
        }
        return $DB_connect->error;
    } else {
        return mysqli_connect_error();
    }
}