<?php
require_once PATH_LIB . 'DbiAdmin.php';

$hospitalName = isset($_GET['hospital_name']) ? $_GET['hospital_name'] : '';
$guardianId = isset($_GET['patient_id']) ? $_GET['patient_id'] : '';
$patientName = isset($_GET['patient_name']) ? $_GET['patient_name'] : '';

$ret = DbiAdmin::getDbi()->getNotice($hospitalName, $guardianId, $patientName);
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