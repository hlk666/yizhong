<?php
require_once PATH_LIB . 'DbiAdmin.php';

$hospitalId = isset($_GET['hospital_id']) ? $_GET['hospital_id'] : 0;
$patientId = isset($_GET['patient_id']) ? $_GET['patient_id'] : 0;
$deviceId = isset($_GET['device_id']) ? $_GET['device_id'] : 0;
$user = isset($_GET['user']) ? $_GET['user'] : 0;

$ret = DbiAdmin::getDbi()->getCommunication($hospitalId, $deviceId, $user, $patientId);
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