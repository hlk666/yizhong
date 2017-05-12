<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_ROOT . 'lib/tool/HpMessage.php';

//2017/04/20
if (isset($_POST['device_type']) && $_POST['device_type'] == '1') {
    api_exit_success();
}

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['upload_url']) && false === Validate::checkRequired($_POST['fail_flag'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'upload_url.']);
}
/*
if (false === Validate::checkRequired($_POST['device_type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_type.']);
}*/

$guardianId = $_POST['patient_id'];
if (strpos($guardianId, '.') !== false) {
    $guardianId = substr($guardianId, 0, -1);
}

$url = isset($_POST['upload_url']) ? $_POST['upload_url'] : '';
$deviceType = isset($_POST['device_type']) ? $_POST['device_type'] : 0;
$failFlag = isset($_POST['fail_flag']) ? $_POST['fail_flag'] : 0;

$hospitalId = DbiAnalytics::getDbi()->getHospitalByPatient($guardianId);
if (1 == $failFlag) {
    if (VALUE_DB_ERROR === $hospitalId || empty($hospitalId)) {
        //do nothing.
    } else {
        setNotice($hospitalId, 'upload_data_fail', $guardianId);
    }
    api_exit_success();
} else {
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data_fail' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
    if (file_exists($file)) {
        clearNotice($hospitalId, 'upload_data_fail', $guardianId);
    }
}
$ret = DbiAnalytics::getDbi()->addGuardianData($guardianId, $url, $deviceType);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (1 == $deviceType) {
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
