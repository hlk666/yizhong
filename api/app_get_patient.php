<?php
require '../config/path.php';
require PATH_CONFIG . 'value.php';
require PATH_LIB . 'Dbi.php';

if (empty($_GET['device_id'])) {
    echo json_encode(['code' => '1', 'message' => 'device_id is empty.']);
    exit;
}

$deviceId = $_GET['device_id'];
if (empty($deviceId)) {
    echo json_encode(['code' => 1, 'message' => 'param of "device_id" is required.']);
    exit;
}
$result = array();
$patient = Dbi::getDbi()->getPatientByDevice($deviceId);
if (VALUE_DB_ERROR == $patient) {
    $result['code'] = 1;
    $result['message'] = 'db error.';
} elseif (empty($patient)) {
    $result['code'] = 2;
    $result['message'] = 'no patient.';
} else {
    $result['code'] = 0;
    $result['patient_id'] = $patient['guardian_id'];
    $result['name'] = $patient['patient_name'];
    $result['mode'] = $patient['mode'];
}
echo json_encode($result);
