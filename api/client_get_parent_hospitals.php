<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$hospitalId = $_GET['hospital_id'];
$ret = Dbi::getDbi()->getHospitalParent($hospitalId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '3', 'message' => '没有上级医院。']);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = '';
    $result['hospitals'] = $ret;
    api_exit($result);
}