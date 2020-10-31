<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}
if (false === Validate::checkRequired($_POST['duty'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'duty.']);
}
if (false === Validate::checkRequired($_POST['area'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'area.']);
}
if (false === Validate::checkRequired($_POST['enter_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'enter_time.']);
}

$ret = DbiSale::getDbi()->addUser($_POST['name'], $_POST['tel'], $_POST['duty'], $_POST['area'], $_POST['enter_time']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
