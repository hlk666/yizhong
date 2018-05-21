<?php
require_once PATH_LIB . 'DbiWX.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_GET['patient_id'];
$status = isset($_GET['status']) && $_GET['status'] == '1' ? '0' : null; 

$ret = DbiWX::getDbi()->getPatientInfoByGuardian($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$patientName = $ret['patient_name'];

$ret = DbiWX::getDbi()->getEcgs($guardianId, $status);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['patient_name'] = $patientName;
    $result['ecgs'] = $ret;

    api_exit($result);
}
