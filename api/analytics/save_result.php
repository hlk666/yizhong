<?php
require_once PATH_LIB . 'Validate.php';
require PATH_ROOT . 'config/diagnosis.php';
require PATH_ROOT . 'lib/DbiAnalytics.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['result'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'result.']);
}

$patientId = $_GET['patient_id'];
//$result = mb_convert_encoding($_POST['result'], 'GBK', 'UTF-8');
$result = $_POST['result'];

$tempArray = explode("\r\n", $result);
$diagnosis = array_intersect($masterDiagnosis, $tempArray);
$keys = array_keys($diagnosis);

foreach ($keys as $key) {
    $ret = DbiAnalytics::getDbi()->addPatientDiagnosis($patientId, $key);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
}

/*
$path = PATH_LONG_RANGE . $patientId . DIRECTORY_SEPARATOR;
if (!file_exists($path)) {
    mkdir($path);
}

$file = $path . 'result.txt';
if (file_exists($file)) {
    unlink($file);
}

$ret = file_put_contents($file, $result);
if (false === $ret) {
    api_exit(['code' => '4', 'message' => 'Server IO error.']);
}
*/
api_exit_success();
