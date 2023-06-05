<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

session_start();

$categories = get_category_query($DB_connect);

if (empty($_SESSION)) {
    $main_content = include_template('403-tmps.php');
    http_response_code(403);
    $title = 403;
}
$title = "Yeticave - мои ставки";

$personal_bets = get_user_bet($DB_connect, $_SESSION['id']);

$main_content = include_template('my-bets-tmps.php', ['bets' => $personal_bets,]);

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => $title,
    'categories' => $categories,
]);

print ($layout_content);