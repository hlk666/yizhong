<?php
require_once PATH_LIB . 'Validate.php';
require PATH_ROOT . 'lib/Dbi.php';

if (false === Validate::checkRequired($_GET['login_user'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'login_user.']);
}
if (false === Validate::checkRequired($_GET['password'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'password.']);
}
$user = $_GET['login_user'];
$password = md5($_GET['password']);

$account = Dbi::getDbi()->getAcount($user);
if (VALUE_DB_ERROR === $account) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($account)) {
    api_exit(['code' => '11', 'message' => '该医生账户不存在。']);
} elseif ($account['password'] != $password) {
    api_exit(['code' => '12', 'message' => '密码错误。']);
} else {
    $hospitalId = $account['hospital_id'];
    
    $fileName = PATH_DATA . 'digital_signature' . DIRECTORY_SEPARATOR . $hospitalId . DIRECTORY_SEPARATOR . $user . '.jpg';
    
    if (!file_exists($fileName)) {
        api_exit(['code' => '4', 'message' => '签名信息不存在。']);
    }
    
    $urlFile = URL_ROOT . "data/digital_signature/$hospitalId/$user.jpg";
    
    api_exit(['code' => 0, 'message' => '', 'url' => $urlFile]);
}
