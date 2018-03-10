<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($_POST['fault'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'fault.']);
}
if (false === Validate::checkRequired($_POST['content'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'content.']);
}

$ret = DbiAdmin::getDbi()->addDeviceFault($_POST['device_id'], $_POST['fault'], $_POST['content']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
