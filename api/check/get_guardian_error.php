<?php
require_once PATH_LIB . 'Dbi.php';

$hospital = isset($_GET['hospital_id']) ? $_GET['hospital_id'] : 0;

$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : null;
$hospital = isset($_GET['hospital_id']) && !empty($_GET['hospital_id']) ? $_GET['hospital_id'] : null;
$notice = isset($_GET['notice']) && !empty($_GET['notice']) ? $_GET['notice'] : null;

$ret = Dbi::getDbi()->getGuardianError($hospital, $notice, $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['guardians'] = $ret;
    api_exit($result);
}