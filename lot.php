<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$categories = get_category_query($DB_connect);

if ($id) {
    $lot = get_lot_query($DB_connect, $id);
    $main_content = include_template('lot-detail-tmps.php', ['lot' => $lot]);
    if (!$lot) {
        $lot['title'] = 404;
        http_response_code(404);
        $main_content = include_template('404-tmps.php');
    }
} else {
    $lot['title'] = 404;
    http_response_code(404);
    $main_content = include_template('404-tmps.php');
}

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => $lot['title'],
    'categories' => $categories,
]);

print ($layout_content);
