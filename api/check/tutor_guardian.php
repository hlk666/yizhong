<?php
require_once PATH_LIB . 'DbiAdmin.php';

$tutorId = isset($_GET['tutor_id']) && !empty($_GET['tutor_id']) ? $_GET['tutor_id'] : null;
$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : null;

$ret = DbiAdmin::getDbi()->getTutorGuardian($tutorId, $startTime, $endTime);
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
