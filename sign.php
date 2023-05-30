<?php

require_once 'helpers.php';
require_once 'data.php';
require_once 'function.php';
require_once 'sql_init.php';

session_start();

$categories = get_category_query($DB_connect);
$main_content = include_template('sign-tmps.php');
$title = 'Yeticave - Страница добавления лота';

if (!empty($_SESSION)) {
    $main_content = include_template('403-tmps.php');
    http_response_code(403);
    $title = 403;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ['email', 'password', 'name', 'message'];

    $existing_users = user_query($DB_connect);
    $existing_email_list = array_column($existing_users, 'email');
    $existing_name_list = array_column($existing_users, 'user_name');

    $rules = [
        'email' => function ($value) use ($existing_email_list) {
        return valid_email($value, $existing_email_list);
        },
        'password' => function ($value) {
        return length_valid($value, 5, 10);
        },
        'message' => function ($value) {
        return length_valid($value, 11, 1000);
        },
        'name' => function ($value) use ($existing_name_list) {
        return valid_name($value, $existing_name_list);
    },
    ];

    $user = filter_input_array(INPUT_POST, [
        'email' => FILTER_DEFAULT,
        'password' => FILTER_DEFAULT,
        'name' => FILTER_DEFAULT,
        'message' => FILTER_DEFAULT,
    ]);

    $errors = field_validation($user, $required, $rules);

    $errors = array_filter($errors);

    if (count($errors)) {
        $main_content = include_template('sign-tmps.php', ['errors' => $errors, 'user' => $user]);
    } else {
        $password = password_hash($user['password'], PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (email, user_name, user_password, contacts) VALUES (?, ?, ?, ?);";
        $stmt = $DB_connect->prepare($sql);
        $stmt->bind_param('ssss', $user['email'], $user['name'], $password, $user['message']);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $user_id = mysqli_insert_id($DB_connect);
            $user_in_db = $DB_connect->execute_query("SELECT user_name FROM users WHERE id = $user_id;")->fetch_assoc();
            $issession = session_start();
            $_SESSION['name'] = $user_in_db['user_name'];
            $_SESSION['id'] = $user_id;

            header("location: /index.php");
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

print ($layout_content);
