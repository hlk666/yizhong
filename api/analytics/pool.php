<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Mqtt.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$status = isset($_POST['status']) && !empty($_POST['status']) ? $_POST['status'] : '6';
$user = isset($_POST['user']) && !empty($_POST['user']) ? $_POST['user'] : '0';

$ret = DbiAnalytics::getDbi()->setPool($_POST['patient_id'], $status, $user);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if ($status == '7') {
    $dbPatient = DbiAnalytics::getDbi()->getPatientWhenUploadData($guardianId);
    if (VALUE_DB_ERROR === $dbPatient || empty($dbPatient)) {
        //do nothing.
    } else {
        setPatient($guardianId, $dbPatient);
        $mqttMessage = 'patient_id=' . $_POST['patient_id']
            . ',url=' . $dbPatient['url']
            . ',upload_time=' . $dbPatient['upload_time']
            . ',device_type=' . $dbPatient['device_type']
            . ',data_status=' . $dbPatient['data_status']
            . ',moved_hospital=' . $dbPatient['moved_hospital']
            . ',moved_hospital_name=' . $dbPatient['moved_hospital_name'];
        $mqtt = new Mqtt();
        $data = [['type' => 'holter', 'id' => '1', 'event'=>'pool', 'message'=>$mqttMessage]];
        $mqtt->publish($data);
    }
}

api_exit_success();
