<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}

$device = $_GET['device_id'];
$hospitalInfo = Dbi::getDbi()->getHospitalByDevice($device);
if (VALUE_DB_ERROR === $hospitalInfo) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($hospitalInfo)) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}
$hospitalId = $hospitalInfo['hospital_id'];

$ret = Dbi::getDbi()->getHospitalParent($hospitalId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    
    $tmp['hospital_id'] = $hospitalId;
    $tmp['hospital_name'] = '本院';
    $ret[] = $tmp;
    $result['hospitals'] = $ret;
    api_exit($result);
}