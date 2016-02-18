<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}

if (false === Validate::checkRequired($_POST['pwd_old'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'pwd_old.']);
}
if (false === Validate::checkRequired($_POST['pwd_new'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'pwd_new.']);
}

$user = $_POST['user'];
$oldPwd = md5($_POST['pwd_old']);
$newPwd = md5($_POST['pwd_new']);

$ret = Dbi::getDbi()->getAcount($user);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
}
if ($ret['password'] != $oldPwd) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
}

$ret = Dbi::getDbi()->updatePassword($user, $newPwd);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
