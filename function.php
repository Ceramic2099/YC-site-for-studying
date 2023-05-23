<?php

function price(int $number): string
{
    if (ceil($number) < 1000) {
        return $number;
    }

    return number_format($number, 0, ".", " ") . " ₽";
}

function get_dt_range(string $date): array
{
    $data_diff = (new DateTime())->diff(new DateTime($date))->format('%d-%H-%I');
    $result_date = explode('-', $data_diff);
    $hours = str_pad($result_date[0] * 24 + $result_date[1], 2, "0", STR_PAD_LEFT);
    $mintes = str_pad(intval($result_date[2]), 2, "0", STR_PAD_LEFT);
    $resulat_array = array($hours, $mintes);

    return $resulat_array;
}

function get_array_or_2dArray($query): array
{
    $num_row = mysqli_num_rows($query);
    if ($num_row === 1) {
        $res_array = mysqli_fetch_assoc($query);
    } else {
        $res_array = mysqli_fetch_all($query, MYSQLI_ASSOC);
    }

    return $res_array;
}

function get_category_query($DB_connect): array
{
    if (!$DB_connect) {
        $error = $DB_connect->connect_error;
        return $error;
    } else {
        $cat_query = $DB_connect->query("SELECT * FROM `categories`");
        if ($cat_query) {
            $categories = $cat_query->fetch_all(MYSQLI_ASSOC);
            return $categories;
        } else {
            $error = $DB_connect->error;
            return $error;
        }
    }
}

function get_lots_query($DB_connect): array
{
    if (!$DB_connect) {
        $error = $DB_connect->connect_error;
        return $error;
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
            $categories = $cat_query->fetch_all(MYSQLI_ASSOC);
            return $categories;
        } else {
            $error = $DB_connect->error;
            return $error;
        }
    }
}

function get_lot_query($DB_connect, $id): array
{
    if (!$DB_connect) {
        $error = $DB_connect->connect_error;
        return $error;
    } else {
        $cat_query = $DB_connect->query(
            "
SELECT l.img, c.name_category, l.title, l.start_price, l.date_finish, l.lot_description
FROM lots l JOIN categories c ON l.category_id=c.id 
WHERE l.id = $id;
"
        );
        if ($cat_query) {
            $lot = get_array_or_2dArray($cat_query);
            return $lot;
        } else {
            $error = $DB_connect->error;
            return $error;
        }
    }
}

function category_valid($id, $allow_list)
{
    if (!in_array($id, $allow_list)) {
        return "Указана не существующая категория";
    }
    return null;
}

function number_valid($num)
{
    if (!empty(intval($num))) {
        if (is_int($num) && $num > 0) {
            return null;
        }
    } else {
        return "Содержимое поля должно быть целым числом больше нуля";
    }
}

function date_valid($date)
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

function valid_email($email, $existing_email_list = 0): ?string
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

function valid_name($name, $existing_name_list): ?string
{
    if (in_array($name, $existing_name_list)) {
        return "Пользовательн с таким именем уже существует";
    } else {
        return null;
    }
}

function user_query($DB_connect)
{
    if (!$DB_connect) {
        $error = $DB_connect->connect_error;
        return $error;
    } else {
        $email_query = $DB_connect->query(
            "
SELECT *
FROM users;
"
        );
        if ($email_query) {
            $existing_email_list = get_array_or_2dArray($email_query);
            return $existing_email_list;
        } else {
            $error = $DB_connect->error;
            return $error;
        }
    }
}

function field_validation($post_array, $required, $rules): array
{
    $errors = [];
    foreach ($post_array as $field => $value) {
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

function length_valid($value, $min, $max)
{
    $len = strlen($value);
    if ($len < $min || $len > $max) {
        return "Значение должно быть от $min до $max симовлов.";
    }
}
