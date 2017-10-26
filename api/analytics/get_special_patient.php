<?php
require_once PATH_LIB . 'Validate.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($data['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$patientId = $data['patient_id'];
$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'special_patient' . DIRECTORY_SEPARATOR . $patientId . '.php';
if (file_exists($file)) {
    echo json_encode(array('code' => '1'));
} else {
    echo json_encode(array('code' => '2'));
}
