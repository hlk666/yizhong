<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}
if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}
if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$loginName = $_POST['user'];
$realName = $_POST['name'];
$password = md5($_POST['password']);
$hospitalId = $_POST['hospital_id'];

$isExisted = Dbi::getDbi()->existedLoginName($loginName);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true === $isExisted) {
    api_exit(['code' => '13', 'message' => '该登录用户名已被他人使用。']);
}

$ret = Dbi::getDbi()->addAccount($loginName, $realName, $password, 2, $hospitalId, 0);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
