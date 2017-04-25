<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
$deviceId = $_GET['device_id'];

$ret = Dbi::getDbi()->getInfoByDevice($deviceId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}
$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['info'] = $ret;
api_exit($result);
