<?php
header("Content-Type:text/html;charset=utf-8");
require '../config/config.php';

if (!isset($_GET['entry']) || empty($_GET['entry'])) {
    echo 'Permission denied!';
    exit;
}

$_GET['entry'] = str_replace('test_', 'client_', $_GET['entry']);

if ($_GET['entry'] == 'special_get_patients'
        || $_GET['entry'] == 'sms'
        || $_GET['entry'] == 'clear_real_time_file') {
    $file = $_GET['entry'] . '.php';
} elseif ($_GET['entry'] == 'app_set_command' 
        || $_GET['entry'] == 'app_set_param' 
        || $_GET['entry'] == 'client_update_param') {
    $file = 'set_command.php';
} else{
    $file = get_file($_GET['entry']);
}
if (!file_exists($file)) {
    echo 'Permission denied!';
    exit;
}

foreach ($_GET as $key => $value) {
    $_GET[$key] = trim($value);
}
foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}

include $file;

function get_file($api)
{
    $route = explode('_', $api, 2);
    return $route[0] . DIRECTORY_SEPARATOR . $route[1] . '.php';
}

function api_exit(array $ret)
{
    if (empty($ret)) {
        $ret = ['code' => '99', 'message' => '发生未知错误，请联系管理员。'];
    }
    echo json_encode($ret);
    exit;
}

function api_exit_success($otherMsg = '')
{
    api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS . $otherMsg]);
}
