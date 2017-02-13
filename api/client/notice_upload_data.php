<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_ROOT . 'lib/tool/HpMessage.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['upload_url'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'upload_url.']);
}
/*
if (false === Validate::checkRequired($_POST['device_type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_type.']);
}*/

$guardianId = $_POST['patient_id'];
$url = $_POST['upload_url'];
$deviceType = isset($_POST['device_type']) ? $_POST['device_type'] : 0;

$ret = DbiAnalytics::getDbi()->addGuardianData($guardianId, $url, $deviceType);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (1 == $deviceType) {
    $hospitalId = DbiAnalytics::getDbi()->getHospitalByPatient($guardianId);
    if (VALUE_DB_ERROR === $hospitalId || empty($hospitalId)) {
        //do nothing.
    } else {
        setNotice($hospitalId, 'upload_data', $guardianId);
    }
} else {
    $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
    if (VALUE_DB_ERROR === $tree || array() == $tree) {
        //do nothing.
    } else {
        setNotice($tree['analysis_hospital'], 'upload_data', $guardianId);
        if ($tree['hospital_id'] != $tree['report_hospital']) {
            setNotice($tree['report_hospital'], 'upload_data', $guardianId);
        }
    }
}

api_exit_success();
