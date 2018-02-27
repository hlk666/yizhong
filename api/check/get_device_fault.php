<?php
require_once PATH_LIB . 'DbiAdmin.php';

$device = isset($_GET['device_id']) ? $_GET['device_id'] : '';
$fault = isset($_GET['fault']) ? $_GET['fault'] : '';

$ret = DbiAdmin::getDbi()->getDeviceFault($device, $fault);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['device_fault'] = $ret;
    api_exit($result);
}