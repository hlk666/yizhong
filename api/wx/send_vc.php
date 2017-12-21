<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'HpVerificationCode.php';
require_once PATH_LIB . 'ShortMessageService.php';
require_once PATH_LIB . 'DbiChronic.php';

if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}

$patientId = DbiChronic::getDbi()->getPatientByTel($_POST['tel']);
if (VALUE_DB_ERROR === $patientId || empty($patientId)) {
    api_exit(['code' => '6', 'message' => '该号码未在系统中注册。']);
}

$vc = HpVerificationCode::createFileNumericVC('Tel' . $_POST['tel']);
if (empty($vc)) {
    api_exit(['code' => '2', 'message' => '发送验证码失败，请重试或者联系管理员。']);
}

$ret = ShortMessageService::send($_POST['tel'], "您正在登录羿中医疗小程序，验证码是【 $vc 】。如果非本人操作，请无视本消息。");
if (false === $ret) {
    api_exit(['code' => '2', 'message' => '发送验证码失败，请重试或者联系管理员。']);
}

api_exit(['code' => '3', 'message' => '发送验证码成功。', 'id' => $patientId]);
