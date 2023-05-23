<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

session_start();

$categories = get_category_query($DB_connect);

$lots = get_lots_query($DB_connect);

$main_content = include_template('main-tmps.php', ['categories' => $categories, 'lots' => $lots]);

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => 'YetiCave - Главная страница',
    'categories' => $categories,
]);

print ($layout_content);
