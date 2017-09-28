<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}

if (false === Validate::checkRequired($_POST['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}

$user = $_POST['user'];
$password = md5($_POST['password']);
$userInfo = DbiAdmin::getDbi()->getUser($user);
if (VALUE_DB_ERROR === $userInfo) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($userInfo)) {
    api_exit(['code' => '11', 'message' => '登录账号错误。']);
} elseif ($userInfo['password'] != $password) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['user'] = $userInfo['user'];
    $result['type'] = $userInfo['type'];
    
    api_exit($result);
}