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
$authcode = isset($_GET['authcode']) ? $_GET['authcode'] : '';

$authData = Dbi::getDbi()->getAuthcode($authcode);
if (VALUE_DB_ERROR === $authData) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$ret = Dbi::getDbi()->getAcount($user);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

//no authcode. 1:old version, pass. 2:new version, check next param.
if (empty($authcode)) {
    if (empty($ret)) {
        api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
    } elseif ($ret['password'] != $pwd) {
        api_exit(['code' => '12', 'message' => '密码错误。']);
    } else {
        //create data at the end.
    }
} elseif (empty($authData)) {
    api_exit(['code' => '26', 'message' => '授权码错误。']);
} else {
    switch ($authData['type']) {
        case '1':   //super.check nothing.get data by user.
            //do nothing here.
            break;
        case '2':   //admin.check password.
            if (empty($ret)) {
                api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
            } elseif ($ret['password'] != $pwd) {
                api_exit(['code' => '12', 'message' => '密码错误。']);
            } else {
                //create data at the end.
            }
            break;
        case '3':   //memeber and doctor. check password and hospial.
            if (empty($ret)) {
                api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
            } elseif ($ret['password'] != $pwd) {
                api_exit(['code' => '12', 'message' => '密码错误。']);
            } elseif ($ret['hospital_id'] != $authData['hospital_id']) {
                api_exit(['code' => '26', 'message' => '授权码错误。']);
            } else {
                //create data at the end.
            }
            break;
        default:
            api_exit(['code' => '99', 'message' => '系统错误。']);
    }
}

if (isset($_GET['exe']) && !empty($_GET['exe'])) {
    Dbi::getDbi()->saveLoginExe($ret['account_id'], $ret['name'], $_GET['exe']);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['account_id'] = $ret['account_id'];
$result['name'] = $ret['name'];
$result['hospital_id'] = $ret['hospital_id'];
$result['type'] = $ret['type'];
$result['agency_id'] = $ret['agency_id'];

$ret = Dbi::getDbi()->getHospitalInfo($result['hospital_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$result['hospital_name'] = $ret['hospital_name'];
$result['upload_flag'] = $ret['upload_flag'];
$result['hospital_type'] = $ret['type'];
$result['relation_level'] = $ret['relation_level'];
api_exit($result);
