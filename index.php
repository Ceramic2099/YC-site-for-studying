<?php

require_once 'helpers.php';
require_once 'data.php';
require_once 'function.php';
require_once 'sql_init.php';

if (!$DB_connect) {
    $error = $DB_connect->connect_error;
}

if(!$cat_query = $DB_connect->query(get_category_query())) {
    $error = $DB_connect->error;
}
$categories = $cat_query->fetch_all(MYSQLI_ASSOC);

if(!$lots_query = $DB_connect->query(get_lots_query())) {
    $error = $DB_connect->error;
}

$lots = $lots_query->fetch_all(MYSQLI_ASSOC);

$main_content = include_template('main.php', ['categories' => $categories, 'lots' => $lots]);

$layout_content = include_template('layout.php', [
    'content' => $main_content,
    'title' => 'YetiCave - Главная страница',
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print ($layout_content);
