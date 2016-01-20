<?php
header("Content-Type:text/html;charset=utf-8");
require '../config/config.php';

if (!isset($_GET['entry']) || empty($_GET['entry'])) {
    echo 'Permission denied!';
    exit;
}

$_GET['entry'] = str_replace('test_', 'client_', $_GET['entry']);

if ($_GET['entry'] == 'app_set_command' 
        || $_GET['entry'] == 'app_set_param' 
        || $_GET['entry'] == 'client_update_param') {
    $file = 'set_command.php';
} else{
    $file = $_GET['entry'] . '.php';
}
if (!file_exists($file)) {
    echo 'Permission denied!';
    exit;
}

function api_exit(array $ret)
{
    if (empty($ret)) {
        $ret = ['code' => '99', 'message' => 'other error.'];
    }
    echo json_encode($ret);
    exit;
}

foreach ($_GET as $key => $value) {
    $_GET[$key] = trim($value);
}
foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}

include $file;
