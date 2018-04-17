<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_POST['patient_id'];
/*
 if (strpos($guardianId, '.') !== false) {
 $guardianId = substr($guardianId, 0, -1);
 }
*/
clearNotice($_POST['hospital_id'], 'data_size', $guardianId);

api_exit_success();

