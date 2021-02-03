<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';
require_once PATH_LIB . 'Mqtt.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_POST['patient_id'];

$ret = Dbi::getDbi()->changeMode($guardianId, '2', '1');
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$invigilator = new Invigilator($guardianId);
$ret = $invigilator->create(['new_mode' => '1']);

if (VALUE_PARAM_ERROR === $ret) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}

$patient = getPatient($guardianId);
$mqttMessage = 'patient_id=' . $patient['guardian_id']
. ',mode=1'
. ',device=' . $patient['device_id']
. ',hospital_id=' . $patient['regist_hospital_id']
. ',hospital_name=' . $patient['regist_hospital_name']
. ',patient_name=' . $patient['patient_name']
. ',sex=' . $patient['sex']
. ',age=' . $patient['age']
. ',start_time=' . $patient['start_time'];
$mqtt = new Mqtt();
$data = [['type' => 'online', 'id' => '1', 'event'=>'add_user', 'message'=>$mqttMessage]];
$mqtt->publish($data);

api_exit_success();
