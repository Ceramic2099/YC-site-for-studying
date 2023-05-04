<?php

$host = 'localhost';
$name = 'root';
$password = '7118335';
$db = 'yeticave';

$DB_connect = new mysqli;
$DB_connect->connect($host,$name,$password,$db);
$DB_connect->set_charset('utf8');
