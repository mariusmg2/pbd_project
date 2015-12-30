<?php

$currency = 'RON ';

$db_username = 'root';
$db_password = '';
$db_name     = 'pbd';
$db_host     = 'localhost';

$shipping_cost = 1.50;

$taxes         = array(
    'Taxa de mediu' => 12,
    'Alte taxe' => 5
);

//connect to MySql
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($mysqli->connect_error) {
    die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

function javaScriptAlertPopup($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '"); </script>';
}
