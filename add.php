<?php

require_once 'helpers.php';
require_once 'data.php';
require_once 'function.php';
require_once 'sql_init.php';

$categories = get_category_query($DB_connect);

$categories_id = array_column($categories, 'id');

$main_content = include_template('add-lot.php', ['categories' => $categories]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['category', 'lot-name', 'message', 'lot-rate', 'lot-step', 'lot-date'];
    $errors = [];

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


    foreach ($lot as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }
        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Поле $field нужно заполнить";
        }
    }

    $errors = array_filter($errors);

    if (!empty($_FILES['lot_img'])) {
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
            'add-lot.php',
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

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'title' => 'Страница добавления лота',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print($layout_content);