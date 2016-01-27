<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['ecg_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'ecg_id.']);
}
if (false === Validate::checkRequired($_POST['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}
if (false === Validate::checkRequired($_POST['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}
if (false === Validate::checkRequired($_POST['content'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'content.']);
}
if (false === Validate::checkRequired($_POST['hospital_type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_type.']);
}
$guardianId = $_POST['patient_id'];
$ecgId = $_POST['ecg_id'];
$doctorId = $_POST['doctor_id'];
$content = $_POST['content'];
$type = $_POST['hospital_type'];

$pwd = md5($_POST['password']);
$ret = Dbi::getDbi()->getAcountById($doctorId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '3', 'message' => '该医生不存在。']);
}
if ($ret['password'] != $pwd) {
    api_exit(['code' => '3', 'message' => '密码有误。']);
}

$ret = Dbi::getDbi()->flowGuardianAddDiagnosis($ecgId, $guardianId, $doctorId, $content, $type);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = '';

api_exit($result);