<?php
require_once PATH_LIB . 'DbiWX.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}

$openId = $_GET['open_id'];
$user = $_GET['user'];
$pasword = md5($_GET['password']);

$ret = DbiWX::getDbi()->clearLogin($_GET['doctor_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
