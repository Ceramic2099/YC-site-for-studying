<?php

require_once 'personal_data.php';

$DB_connect = new mysqli;
$DB_connect->connect($host, $name, $password, $db);
$DB_connect->set_charset('utf8');
