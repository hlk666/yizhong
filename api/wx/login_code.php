<?php
require_once PATH_LIB . 'DbiWX.php';
require_once PATH_LIB . 'Validate.php';

$appid = 'wxac82d342172954c3';
$secret = '6a89528eb524cc19fb2b954660e0046c';

if (false === Validate::checkRequired($_GET['code'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'code.']);
}

$code = $_GET['code'];
$url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=authorization_code";
$wxLoginResult = json_decode(file_get_contents($url));
if (isset($wxLoginResult->errcode) || !isset($wxLoginResult->openid)) {
    api_exit(['code' => '5', 'message' => '请退出重新登录微信。']);
}
$openId = $wxLoginResult->openid;


$DoctorInfo = DbiWX::getDbi()->getDoctorByOpenId($openId);
if (VALUE_DB_ERROR === $DoctorInfo) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($DoctorInfo)) {
    api_exit(['code' => '4', 'open_id' => $openId, 'message' => MESSAGE_DB_NO_DATA]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['open_id'] = $openId;
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

