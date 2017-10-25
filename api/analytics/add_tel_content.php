<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['hospital_name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_name.']);
}
if (false === Validate::checkRequired($_POST['doctor_name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_name.']);
}
if (false === Validate::checkRequired($_POST['content'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'content.']);
}

$guardianId = $_POST['patient_id'];
$hospitalId = $_POST['hospital_id'];
$hospitalName = $_POST['hospital_name'];
$doctorName = $_POST['doctor_name'];
$content = $_POST['content'];
//$result = mb_convert_encoding($_POST['result'], 'GBK', 'UTF-8'); //this action is required if saved in file.


$ret = DbiAnalytics::getDbi()->setTelContent($guardianId, $hospitalId, $hospitalName, $doctorName, $content);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
