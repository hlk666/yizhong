<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user.']);
}

if (false === Validate::checkRequired($_GET['pwd'])) {
    api_exit(['code' => '2', 'message' => MESSAGE_REQUIRED . 'password.']);
}

$user = $_GET['user'];
$pwd = md5($_GET['pwd']);
$ret = Dbi::getDbi()->getAcount($user);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => '用户不存在。']);
} elseif ($ret['password'] != $pwd) {
    api_exit(['code' => '5', 'message' => '密码有误。']);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = '';
    $result['account_id'] = $ret['account_id'];
    $result['name'] = $ret['name'];
    $result['hospital_id'] = $ret['hospital_id'];
    
    $ret = Dbi::getDbi()->getHospitalInfo($result['hospital_id']);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
    }
    $result['hospital_name'] = $ret['hospital_name'];
    api_exit($result);
}