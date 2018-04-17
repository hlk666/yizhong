<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['age'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'age.']);
}
if (false === Validate::checkRequired($_POST['sex'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'sex.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}
if (false === Validate::checkRequired($_POST['regist_hospital'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'regist_hospital.']);
}
if (false === Validate::checkRequired($_POST['doctor_name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_name.']);
}

$hospital = $_POST['regist_hospital'];
$name = $_POST['name'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$tel = $_POST['tel'];
$doctor = $_POST['doctor_name'];

$sickRoom = isset($_POST['sickroom']) ? $_POST['sickroom'] : '';
$bloodPressure = isset($_POST['bloodpress']) ? $_POST['bloodpress'] : '';
$height = isset($_POST['height']) ? $_POST['height'] : '0';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '0';
$familyName = isset($_POST['family_name']) ? $_POST['family_name'] : '0';
$familyTel = isset($_POST['family_tel']) ? $_POST['family_tel'] : '0';
$tentativeDiagnosis = isset($_POST['tentative_diagnose']) ? $_POST['tentative_diagnose'] : '';
$medicalHistory = isset($_POST['medical_history']) ? $_POST['medical_history'] : '';
$hospitalization = isset($_POST['hospitalization_id']) ? $_POST['hospitalization_id'] : '0';

$orderId = Dbi::getDbi()->addOrder($hospital, $name, $sex, $age, $tel, $sickRoom, $bloodPressure, $height, 
        $weight, $familyName, $familyTel, $tentativeDiagnosis, $medicalHistory, $hospitalization, $doctor);
if (VALUE_DB_ERROR === $orderId) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
