<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['hospital'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital.']);
}

$ret = Dbi::getDbi()->getRepeatPatient($_POST['hospital'], $_POST['name']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (!empty($ret)) {
    api_exit(['code' => '18', 'message' => '重复注册。', 'data' => $ret]);
}

api_exit_success();
