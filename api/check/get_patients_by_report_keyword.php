<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['keyword'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'keyword.']);
}

$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : null;

$ret = DbiAdmin::getDbi()->getGuardianByReportKeyword($_GET['keyword'], $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'list' => $ret]);
}
