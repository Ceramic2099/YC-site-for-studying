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

function get_category_query(): string
{
    return "SELECT * FROM `categories`";
}
function get_lots_query(): string
{
    return "
SELECT l.img, c.name_category, l.title, l.start_price, l.date_finish 
FROM lots l JOIN categories c ON l.category_id=c.id 
WHERE l.date_finish > NOW() 
ORDER BY l.date_creation DESC
";
}
