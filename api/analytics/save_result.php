<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['result'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'result.']);
}

$patientId = $_POST['patient_id'];
//$result = mb_convert_encoding($_POST['result'], 'GBK', 'UTF-8'); //this action is required if saved in file.
$result = $_POST['result'];

$keys = explode(',', $result);
//$diagnosis = array_intersect($masterDiagnosis, $tempArray);
//$keys = array_keys($diagnosis);

$ret = DbiAnalytics::getDbi()->setHeavy($patientId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

foreach ($keys as $key) {
    $ret = DbiAnalytics::getDbi()->addPatientDiagnosis($patientId, $key);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
}

$hospital = DbiAnalytics::getDbi()->getHospitalByPatient($patientId);
if (VALUE_DB_ERROR === $hospital || empty($hospital)) {
    //not notice
} else {
    setNotice($hospital, 'diagnosis', $patientId);
}

api_exit_success();
