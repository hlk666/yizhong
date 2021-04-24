<?php
require_once PATH_LIB . 'QinFangKangJian.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['t'])) {
    api_exit1(['code' => '1', 'message' => MESSAGE_REQUIRED . 'timestamp.']);
}
if (false === Validate::checkRequired($_GET['k'])) {
    api_exit1(['code' => '1', 'message' => MESSAGE_REQUIRED . 'key.']);
}
if (false === Validate::checkRequired($_GET['id'])) {
    api_exit1(['code' => '1', 'message' => MESSAGE_REQUIRED . 'id.']);
}

$time = $_GET['t'];
$key = $_GET['k'];
$guardianId = $_GET['id'];

$obj = new QinFangKangJian();
$ret = $obj->checkBaseParam($time, $key);
if ($ret != $obj->VALUE_OK) {
    api_exit1(['code' => '1', 'message' => $obj->MESSAGE_AUTH . $ret]);
}


$ret = $obj->getRegistInfo($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit1(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit1(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['data'] = $ret;
    api_exit1($result);
}

function api_exit1(array $ret)
{
    if (empty($ret)) {
        $ret = ['code' => '99', 'message' => '发生未知错误，请联系管理员。'];
    }
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}