<?php

require_once 'helpers.php';
require_once 'function.php';
require_once 'sql_init.php';

session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$categories = get_category_query($DB_connect);


if ($id) {
    $lot = get_lot_query($DB_connect, $id);
    $bets = get_bet_query($DB_connect, $id);
    $main_content = include_template('lot-detail-tmps.php', ['lot' => $lot, 'bets' => $bets]);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION)) {

    $values = filter_input_array(INPUT_POST, [
        'lot_id' => FILTER_SANITIZE_NUMBER_INT,
        'cost' => FILTER_SANITIZE_NUMBER_INT,
    ]);

    $lot = get_lot_query($DB_connect, $values['lot_id']);
    $bets = get_bet_query($DB_connect, $values['lot_id']);

    $required = ['cost'];

    if (!empty($bets)) {
        $last_bet = $bets[0]['price_bet'] + $lot['step'];
    } else {
        $last_bet = $lot['start_price'] + $lot['step'];
    }

    $rules = [
        'cost' => function ($value) use ($last_bet) {
            return price_bet_valid($value, $last_bet);
        }];

    $errors = field_validation($values, $required, $rules);
    $errors = array_filter($errors);

    if (count($errors)) {
        $main_content = include_template('lot-detail-tmps.php', ['lot' => $lot, 'errors' => $errors, 'bets' => $bets]
        );
    } else {
        $user = $_SESSION['id'];
        $sql = "INSERT INTO bets(date_bet, price_bet, user_id, lot_id) VALUES (NOW(),?,?,?);";
        $stmt = $DB_connect->prepare($sql);
        $stmt->bind_param('iii', $values['cost'], $user, $lot['id']);
        $res = $stmt->execute();

        if ($res) {
            header("location: /lot.php?id=" . $values['lot_id']);
        } else {
            $error = mysqli_error($DB_connect);
        }
    }

}

$layout_content = include_template('layout-tmps.php', [
    'content' => $main_content,
    'title' => $lot['title'],
    'categories' => $categories,
]);

print ($layout_content);
