<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

$categories = get_category_query($DB_connect);

$main_content = include_template('login-tmps.php');

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $required = ['email', 'password'];

    $rules = [
        'email' => function ($value) {
            return valid_email($value);
        },
        'password' => function ($value) {
            return length_valid($value, 5, 10);
        },
    ];

    $user = filter_input_array(INPUT_POST, [
        'email' => FILTER_DEFAULT,
        'password' => FILTER_DEFAULT,
    ]);

    $errors = field_validation($user, $required, $rules);
    $errors = array_filter($errors);

    if (count($errors)) {
        $main_content = include_template('login-tmps.php', ['errors' => $errors, 'user' => $user]);
    } else {
        $sql = "SELECT * FROM users WHERE email = ?;";
        $stmt = $DB_connect->prepare($sql);
        $stmt->bind_param('s', $user['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_in_db = $result->fetch_assoc();

        if ($user_in_db) {
            if (password_verify($user['password'], $user_in_db['user_password'])) {
                $issession = session_start();
                $_SESSION['name'] = $user_in_db['user_name'];
                $_SESSION['id'] = $user_in_db['id'];

                header("Location: /index.php");
            } else {
                $errors["password"] = "Пароль не совпадает";
            }
        } else {
            $errors["email"] = "Такой пользователь не зарегестрирован";
        }

        if (count($errors)) {
            $main_content = include_template('login-tmps.php', ['errors' => $errors, 'user' => $user]);
        }
    }

}

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => 'Yeticave - Страница добавления лота',

    'categories' => $categories,
]);

print($layout_content);