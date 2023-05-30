<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

session_start();

$categories = get_category_query($DB_connect);
$categories_id = array_column($categories, 'id');
$main_content = include_template('add-lot-tmps.php', ['categories' => $categories]);
$title = 'Yeticave - Страница добавления лота';

if (empty($_SESSION)) {
    $main_content = include_template('403-tmps.php');
    http_response_code(403);
    $title = 403;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['category', 'lot-name', 'message', 'lot-rate', 'lot-step', 'lot-date'];

    $rules = [
        "category" => function ($value) use ($categories_id) {
            return category_valid($value, $categories_id);
        },
        "lot-rate" => function ($value) {
            return number_valid($value);
        },
        "lot-step" => function ($value) {
            return number_valid($value);
        },
        "lot-date" => function ($value) {
            return date_valid($value);
        }
    ];


    $lot = filter_input_array(INPUT_POST, [
        'category' => FILTER_DEFAULT,
        'lot-name' => FILTER_DEFAULT,
        'message' => FILTER_DEFAULT,
        'lot-rate' => FILTER_DEFAULT,
        'lot-step' => FILTER_DEFAULT,
        'lot-date' => FILTER_DEFAULT,
    ]);

    $errors = field_validation($lot, $required, $rules);

    $errors = array_filter($errors);

    if (!empty($_FILES['lot_img']["name"])) {
        $tmp = $_FILES['lot_img']['tmp_name'];

        $mime_type = mime_content_type($tmp);
        switch ($mime_type) {
            case "image/jpeg" :
                $ext = '.jpeg';
                break;
            case "image/png" :
                $ext = '.png';
                break;
            case "image/jpg" :
                $ext = '.jpg';
                break;
        }
        if ($ext) {
            $filename = uniqid() . $ext;
            $lot['path'] = "uploads/" . $filename;
            move_uploaded_file($tmp, $lot['path']);
        } else {
            $errors['lot_img'] = "Допустимые форматы файлов: jpg, jpeg, png";
        }
    } else {
        $errors['lot_img'] = "Вы не загрузили изображение";
    }

    if (count($errors)) {
        $main_content = include_template(
            'add-lot-tmps.php',
            ['categories' => $categories, 'lot' => $lot, 'errors' => $errors]
        );
    } else {
        $user = 1;
        $sql = "INSERT INTO lots (title, category_id, lot_description, start_price, step, date_finish, img, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
        $stmt = $DB_connect->prepare($sql);
        $stmt->bind_param('sisiissi', $lot['lot-name'], $lot['category'], $lot['message'], $lot['lot-rate'], $lot['lot-step'], $lot['lot-date'], $lot['path'], $user);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $lot_id = mysqli_insert_id($DB_connect);
            header("location: /lot.php?id=" . $lot_id);
        } else {
            $error = mysqli_error($DB_connect);
        }
    }
}

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => $title,
    'categories' => $categories,
]);

print($layout_content);