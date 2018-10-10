<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['department_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'department_id.']);
}
/*
if (false === Validate::checkRequired($_POST['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}
*/
if (false === Validate::checkRequired($_POST['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}
if (empty($_POST['user']) && empty($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user | tel.']);
}

$password = md5($_POST['password']);
$ret = DbiEcgn::getDbi()->login($_POST['department_id'], $_POST['user'], $_POST['tel']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
} elseif ($ret['password'] != $password) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['doctor_id'] = $ret['doctor_id'];
    $result['name'] = $ret['name'];
    //$result['hospital_id'] = $ret['hospital_id'];
    $result['type'] = $ret['type'];
    /*
    $ret = Dbi::getDbi()->getHospitalInfo($result['hospital_id']);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    $result['hospital_name'] = $ret['hospital_name'];
    $result['upload_flag'] = $ret['upload_flag'];
    $result['hospital_type'] = $ret['type'];
    */
    api_exit($result);
}