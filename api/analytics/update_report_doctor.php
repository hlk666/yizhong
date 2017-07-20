<?php
require_once PATH_LIB . 'Validate.php';
require PATH_ROOT . 'lib/DbiAnalytics.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

if (false === Validate::checkRequired($_POST['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}

$guardianId = $_POST['patient_id'];
$doctorId = $_POST['doctor_id'];

$ret = DbiAnalytics::getDbi()->setDataStatus($guardianId, 'report', '0', $doctorId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
