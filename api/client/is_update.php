<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$hospital = $_GET['hospital_id'];
$file = PATH_CONFIG . 'is_update.txt';
if (!file_exists($file)) {
    api_exit(['code' => '6', 'message' => '配置信息错误，请联系管理员。']);
}

$config = explode(',', file_get_contents($file));
if (in_array($hospital, $config)) {
    $ret = '1';
} else {
    $ret = '0';
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'is_update' => $ret]);
