<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'id.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}

$user = $_POST['user'];
$oldPwd = md5($_POST['pwd_old']);
$newPwd = md5($_POST['pwd_new']);

$ret = Dbi::getDbi()->getAcountById($_POST['id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
}

$ret = Dbi::getDbi()->updateDoctorType($_POST['id'], $_POST['type']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
