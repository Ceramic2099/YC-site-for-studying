<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

$categories = get_category_query($DB_connect);
$title = 'Yeticave - Страница поиска лота';

$search = strip_tags($_GET['search']);
$search = htmlspecialchars($search);

if (!empty($search)) {
    $lot_count = get_count_lots($DB_connect, $search);
    $page_number = $_GET['page'] ?? 1;
    $item_per_page = 3;
    $total_page = ceil($lot_count / $item_per_page);
    $offset = ($page_number - 1) * $item_per_page;
    $pages = range(1, $total_page);
    $lots = get_found_lots($DB_connect, $search, $item_per_page, $offset);
}

$main_content = include_template('search-tmps.php', [
    'search' => $search,
    'lots' => $lots,
    'total_page' => $total_page,
    'pages' => $pages,
    'page_number' => $page_number,
]);

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => $title,
    'categories' => $categories,
]);

print($layout_content);