<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

validate_add_user($_POST);

$name = $_POST['name'];
$sex = $_POST['sex'];
$age = $_POST['age'];
$tel = $_POST['tel'];
$device = isset($_POST['device_id']) ? $_POST['device_id'] : '99999';
$registHospital = $_POST['regist_hospital'];
$guardHospital = $_POST['regist_hospital'];
$mode = '4';
$hours = isset($_POST['guard_hours']) ? $_POST['guard_hours'] : 24;
$doctorId = 0;
$sickRoom = isset($_POST['sickroom']) ? $_POST['sickroom'] : '';
$bloodPressure = isset($_POST['blood_pressure']) ? $_POST['blood_pressure'] : '';
$height = isset($_POST['height']) ? $_POST['height'] : '0';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '0';
$familyTel = isset($_POST['family_tel']) ? $_POST['family_tel'] : '0';
$tentativeDiagnose = isset($_POST['tentative_diagnose']) ? $_POST['tentative_diagnose'] : '';
$medicalHistory = isset($_POST['medical_history']) ? $_POST['medical_history'] : '';
$doctorName = $_POST['doctor_name'];
$hospitalizationId = isset($_POST['hospitalization_id']) ? $_POST['hospitalization_id'] : '0';

$guardianId = Dbi::getDbi()->flowGuardianAddUser($name, $sex, $age, $tel, $device, $registHospital, 
        $guardHospital, $mode, $hours, PARAM_LEAD, $doctorId, $sickRoom, $bloodPressure, $height, $weight,
        $familyTel, $tentativeDiagnose, $medicalHistory, $doctorName, $hospitalizationId);
if (VALUE_DB_ERROR === $guardianId) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success($guardianId);

function validate_add_user($post)
{
    if (!is_array($post) || empty($post)) {
        api_exit(['code' => '1', 'message' => '没有传递任何参数。']);
    }
    if (false === Validate::checkRequired($post['name'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
    }
    if (false === Validate::checkRequired($post['age'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'age.']);
    }
    if (false === Validate::checkRequired($post['sex'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'sex.']);
    }
    if (false === Validate::checkRequired($post['tel'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
    }
    if (false === Validate::checkRequired($post['regist_hospital'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'regist_hospital.']);
    }
    if (false === Validate::checkRequired($post['doctor_name'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_name.']);
    }
}
