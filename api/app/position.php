<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Dbi.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($_POST['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($_POST['X'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'X坐标.']);
}
if (false === Validate::checkRequired($_POST['Y'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'Y坐标.']);
}

$ret = Dbi::getDbi()->addDevicePosition($_POST['device_id'], $_POST['X'], $_POST['Y']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
