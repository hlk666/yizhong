<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}

if (false === Validate::checkRequired($_POST['old_password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'old_password.']);
}
if (false === Validate::checkRequired($_POST['new_password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'new_password.']);
}

$user = $_POST['user'];
$oldPassword = md5($_POST['old_password']);
$newPassword = md5($_POST['new_password']);

$userInfo = DbiAdmin::getDbi()->getUser($user);
if (VALUE_DB_ERROR === $userInfo) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($userInfo)) {
    api_exit(['code' => '11', 'message' => '登录账号错误。']);
}
if ($userInfo['password'] != $oldPassword) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
}

$ret = DbiAdmin::getDbi()->updatePassword($user, $newPassword);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
