<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($_GET['start_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'start_time.']);
}

$data = DbiAdmin::getDbi()->getDeviceCommunication($_GET['device_id'], $_GET['start_time']);
if (VALUE_DB_ERROR === $data) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($data)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $ret['code'] = '0';
    $ret['message'] = MESSAGE_SUCCESS;
    $ret['data'] = $data;
    api_exit($ret);
}