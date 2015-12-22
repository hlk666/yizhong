<?php
require '../config/config.php';

if (!isset($_GET['entry']) || empty($_GET['entry'])) {
    echo 'Permission denied!';
    exit;
}
if ($_GET['entry'] == 'app_set_command' || $_GET['entry'] == 'app_set_param') {
    $file = 'set_command.php';
} else{
    $file = $_GET['entry'] . '.php';
}
if (!file_exists($file)) {
    echo 'Permission denied!';
    exit;
}
require $file;
