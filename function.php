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

function get_array_or_2dArray ($query): array
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
