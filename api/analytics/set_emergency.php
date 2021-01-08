<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Mqtt.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['reporter_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'reporter_id.']);
}

$ret = DbiAnalytics::getDbi()->addEmergency($_POST['patient_id'], $_POST['reporter_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$patient = getPatient($_POST['patient_id']);
$mqttMessage = 'patient_id=' . $patient['guardian_id']
. ',device=' . $patient['device_id']
. ',hospital_id=' . $patient['regist_hospital_id']
. ',hospital_name=' . $patient['regist_hospital_name']
. ',patient_name=' . $patient['patient_name']
. ',emergency_id=' . $ret;
$mqtt = new Mqtt();
$data = [['type' => 'holter', 'id' => '1', 'event'=>'emergency', 'message'=>$mqttMessage]];
$mqtt->publish($data);

api_exit_success();
