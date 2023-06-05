<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'sql_init.php';
require_once 'function.php';
require_once './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once './vendor/phpmailer/phpmailer/src/SMTP.php';
require_once './vendor/phpmailer/phpmailer/src/Exception.php';

$lots_wo_winners = get_lots_wo_winners($DB_connect);

$list_of_last_bet = [];

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->SMTPDebug = 0;
$mail->Host = 'app.debugmail.io';
$mail->Port = '25';
$mail->Username = "f1f4abc2-47a7-44c6-b723-c6200acf1430";
$mail->Password = "abf2cc70-ae96-45c1-ad82-67bccaa905f5";
$mail->setFrom('yeticave-admin@mail.com', 'Administartor');
$mail->Subject = 'You are winner!';

if ($lots_wo_winners) {
    foreach ($lots_wo_winners as $key => $value) {
        $last_bet = get_last_bet($DB_connect, $value['id']);
        $list_of_last_bet = array_merge($list_of_last_bet, $last_bet);
    }
}

if ($list_of_last_bet) {
    foreach ($list_of_last_bet as $key => $value) {
        add_winners($DB_connect, $value['lot_id'], $value['user_id']);
        $title = $value["title"];
        try {
            $mail->msgHTML("<p><strong>You are win lot '$title' Congratulation!</strong></p>");
            $mail->addAddress($value['email']);
            $mail->send();
        } catch (Exception $e) {
            echo 'Something gone wrong', $e->getMessage(); "\n";
        }
    }
}



