<?php
require '../config/config.php';
require PATH_LIB . 'Dbi.php';

if (empty($_GET['device_id'])) {
    echo json_encode(['code' => '1', 'message' => MESSAGE_REQUIRED .'device_id']);
    exit;
}

$deviceId = $_GET['device_id'];
$result = array();
$patient = Dbi::getDbi()->getPatientByDevice($deviceId);
if (VALUE_DB_ERROR === $patient) {
    $result['code'] = 1;
    $result['message'] = MESSAGE_DB_ERROR;
} elseif (empty($patient)) {
    $result['code'] = 2;
    $result['message'] = MESSAGE_DB_NO_DATA;
} else {
    $result['code'] = 0;
    $result['patient_id'] = $patient['guardian_id'];
    $result['name'] = $patient['patient_name'];
    $result['mode'] = $patient['mode'];
}
echo json_encode($result);
