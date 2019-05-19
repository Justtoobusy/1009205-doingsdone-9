<?php
$con = mysqli_connect('localhost', 'root', '', 'doingsdone');
mysqli_set_charset($con, 'utf8');
$is_auth = false;
session_start();
if (isset($_SESSION['user'])) {
    $is_auth = true;
}
