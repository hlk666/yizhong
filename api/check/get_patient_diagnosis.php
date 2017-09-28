<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospitals.']);
}
if (false === Validate::checkRequired($_GET['diagnosis'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'diagnosis.']);
}

$hospital = $_GET['hospitals'];
$diagnosis = $_GET['diagnosis'];
$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;

$ret = DbiAdmin::getDbi()->getPatientDiagnosis($hospital, $diagnosis, $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $total = DbiAdmin::getDbi()->getTotalDiagnosis($hospital, $startTime, $endTime);
    if (VALUE_DB_ERROR === $total) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (empty($total)) {
        $total = 0;
    }
    
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['patients'] = $ret;
    $result['total'] = $total;
    api_exit($result);
}
