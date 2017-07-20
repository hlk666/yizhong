<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
$guardianId = $_GET['patient_id'];

$hospitalConfig = DbiAnalytics::getDbi()->getHospitalConfig($guardianId);
if (VALUE_DB_ERROR === $hospitalConfig) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$doubleHospitals = [131];
if (empty($hospitalConfig)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    if (in_array($hospitalConfig['hospital_id'], $doubleHospitals)) {
        $hospitalConfig['double'] = '1';
    } else {
        $hospitalConfig['double'] = '0';
    }

    $hospitalConfig['code'] = '0';
    $hospitalConfig['message'] = MESSAGE_SUCCESS;
    api_exit($hospitalConfig);
}
