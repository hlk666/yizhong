<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'HpVerificationCode.php';
require_once PATH_LIB . 'DbiChronic.php';

if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}
if (false === Validate::checkRequired($_POST['vc'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'vc.']);
}

$patientId = DbiChronic::getDbi()->getPatientByTel($_POST['tel']);
if (VALUE_DB_ERROR === $patientId || empty($patientId)) {
    api_exit(['code' => '6', 'message' => '该号码未在系统中注册。']);
}

$vc = HpVerificationCode::getVC('Tel' . $_POST['tel']);
if (empty($vc) || $vc != $_POST['vc']) {
    api_exit(['code' => '4', 'message' => '验证码错误。']);
}

api_exit(['code' => '5', 'message' => '验证码正确。', 'id' => $patientId]);
