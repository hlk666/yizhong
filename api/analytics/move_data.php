<?php
require_once PATH_LIB . 'Validate.php';
require PATH_ROOT . 'config/diagnosis.php';
require PATH_ROOT . 'lib/DbiAnalytics.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
/*
if (false === Validate::checkRequired($_POST['hospital_from'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_from.']);
}
*/
if (false === Validate::checkRequired($_POST['hospital_to'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_to.']);
}
if (false === Validate::checkRequired($_POST['operator'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'operator.']);
}

$guardianId = $_POST['patient_id'];
$operator = $_POST['operator'];
//$hospitalFrom = $_POST['hospital_from'];
$hospitalTo = $_POST['hospital_to'];

if (false == DbiAnalytics::getDbi()->existedHospital($hospitalTo)) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM . 'hospital_to.']);
}


//$hospitalFromDB = DbiAnalytics::getDbi()->getHospitalByPatient($guardianId);
$hospitalFrom = DbiAnalytics::getDbi()->getHospitalByPatient($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
/*
if ($hospitalFrom != $hospitalFromDB) {
    api_exit(['code' => '3', 'message' => '移动对象医院的ID错误。']);
}
*/
$ret = DbiAnalytics::getDbi()->moveData($guardianId, $hospitalFrom, $hospitalTo, $operator);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

setNotice($hospitalTo, 'move_data', $guardianId);

api_exit_success();