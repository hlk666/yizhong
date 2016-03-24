<?php
require_once PATH_LIB . 'DataFile.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_GET['alert'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'alert.']);
}
// if (false === Validate::checkRequired($_GET['time'])) {
//     api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'time.']);
// }

$patientId = $_GET['patient_id'];
$alertType = $_GET['alert'];
$logFile = 'add_alert.log';
$time = isset($_GET['time']) ? date('Y-m-d H:i:s', $_GET['time']/1000) : date('Y-m-d H:i:s');

$dataFile = DataFile::getDataFile('alert_sum', $patientId);
if (false === $dataFile) {
    $alertSum = array();
    $alertSum[$alertType] = 1;
} else {
    include $dataFile;
    if (isset($alertSum[$alertType])) {
        $alertSum[$alertType] += 1;
    } else {
        $alertSum[$alertType] = 1;
    }
}

Logger::write($logFile, "patient_id is $patientId, type type is $alertType, alert time is $time.");
DataFile::setDataFile('alert_sum', $patientId, ['alertSum' => $alertSum]);


api_exit_success();
