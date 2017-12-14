<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}

if (false === Validate::checkRequired($_GET['pwd'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}

$user = $_GET['user'];
$pwd = md5($_GET['pwd']);
$ret = Dbi::getDbi()->getAcount($user);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
} elseif ($ret['password'] != $pwd) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['account_id'] = $ret['account_id'];
    $result['name'] = $ret['name'];
    $result['hospital_id'] = $ret['hospital_id'];
    $result['type'] = $ret['type'];
    
    $ret = Dbi::getDbi()->getHospitalInfo($result['hospital_id']);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    $result['hospital_name'] = $ret['hospital_name'];
    $result['upload_flag'] = $ret['upload_flag'];
    $result['hospital_type'] = $ret['type'];
    api_exit($result);
}