<?php
require_once PATH_LIB . 'DbiWX.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['open_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'open_id.']);
}
if (false === Validate::checkRequired($_GET['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}
if (false === Validate::checkRequired($_GET['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}

$openId = $_GET['open_id'];
$user = $_GET['user'];
$pasword = md5($_GET['password']);

$DoctorInfo = DbiWX::getDbi()->getDoctorByUser($user);
if (VALUE_DB_ERROR === $DoctorInfo) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($DoctorInfo)) {
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
} elseif ($DoctorInfo['password'] != $pasword) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
} else {
    $ret = DbiWX::getDbi()->updateOpenId($user, $openId);
    if (VALUE_DB_ERROR === $DoctorInfo) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['doctor_id'] = $DoctorInfo['doctor_id'];
$result['doctor_name'] = $DoctorInfo['doctor_name'];
$result['hospital_id'] = $DoctorInfo['hospital_id'];
$result['type'] = $DoctorInfo['type'];

$hospitalInfo = DbiWX::getDbi()->getHospitalInfo($DoctorInfo['hospital_id']);
if (VALUE_DB_ERROR === $DoctorInfo['hospital_id']) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$result['hospital_name'] = $hospitalInfo['hospital_name'];
api_exit($result);

