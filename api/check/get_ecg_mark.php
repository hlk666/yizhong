<?php
require_once PATH_LIB . 'DbiAdmin.php';

$startTime = isset($_GET['start_time']) ? $_GET['start_time'] : '';
$endTime = isset($_GET['end_time']) ? $_GET['end_time'] : '';

$ret = DbiAdmin::getDbi()->getEcgMark($startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['list'] = $ret;
    api_exit($result);
}