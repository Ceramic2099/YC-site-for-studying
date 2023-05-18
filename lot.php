<?php

require_once 'helpers.php';
require_once 'data.php';
require_once 'function.php';
require_once 'sql_init.php';

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

$categories = get_category_query($DB_connect);

$page_404 = include_template('404-tmps.php', ['categories' => $categories]);

if ($id) {
    $lot = get_lot_query($DB_connect, $id);
    if (!$lot) {
        print($page_404);
    }
} else {
    print($page_404);
}

$main_content = include_template('lot-detail-tmps.php', ['lot' => $lot]);

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => $lot['title'],
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'categories' => $categories,
]);

print ($layout_content);
