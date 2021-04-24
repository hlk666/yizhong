<?php
require_once PATH_LIB . 'Validate.php';
require PATH_ROOT . 'lib/DbiHebei.php';

if (false === Validate::checkRequired($_GET['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}


$doctor = DbiHebei::getDbi()->getYizhongDoctor($_GET['doctor_id']);
if (VALUE_DB_ERROR === $doctor) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($doctor)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} 

api_exit(['code' => 0, 'message' => MESSAGE_SUCCESS, 'doctor_id' => $doctor['doctor_id'], 'doctor_name' => $doctor['doctor_name']]);
