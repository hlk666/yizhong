<?php
error_reporting(E_ERROR | E_PARSE);

require PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_ROOT . 'vendor/nusoap-0.9.5/lib/nusoap.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}

$iccid = DbiAdmin::getDbi()->getICCID($_GET['device_id']);
if (VALUE_DB_ERROR === $iccid) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($iccid)) {
    api_exit(['code' => '3', 'message' => '该设备不存在或对应的联通管理ID未注册。']);
}

$wsdlUrl = '';
$licenseKey = '116ce7e9-6f1a-4ac0-8c39-28d280b8b458';
$userName = 'lili123';
$password = '123456yizhong';

$service = new nusoap_client('https://api.10646.cn/ws/schema/Terminal.wsdl', true);
$service->setHeaders(
        '<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">'.
        '<wsse:UsernameToken>'.
        '<wsse:Username>'.$userName.'</wsse:Username>'.
        '<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$password.'</wsse:Password>'.
        '</wsse:UsernameToken>'.
        '</wsse:Security>'
);
$msg =
'<GetSessionInfoRequest xmlns="http://api.jasperwireless.com/ws/schema">'.
'<messageId></messageId>'.
'<version></version>'.
'<licenseKey>'.$licenseKey.'</licenseKey>'.
'<iccid>' . $iccid . '</iccid>'.
'</GetSessionInfoRequest>';
$result = $service->call('GetSessionInfo', $msg);

if ($service->fault) {
    /*
    echo 'faultcode: ' . $service->faultcode . "\n";
    echo 'faultstring: ' . $service->faultstring . "\n";
    echo 'faultDetail: ' . $service->faultdetail . "\n";
    echo 'response: ' . $service->response;
    exit(0);
    */
    Logger::write('10646.log', $service->faultstring . $service->faultdetail);
    api_exit(['code' => '4', 'message' => '调用联通接口时发生错误。']);
}
if (empty($result['sessionInfo'])) {
    $online = '0';
} else {
    if (empty($result['sessionInfo']['session']['ipAddress'])) {
        $online = '0';
    } else {
        $online = '1';
    }
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'online' => $online]);
