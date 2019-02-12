<?php
require_once PATH_LIB . 'Dbi.php';

$hospital = isset($_GET['hospital_id']) ? $_GET['hospital_id'] : 0;

$ret = Dbi::getDbi()->getGuardianError($hospital);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['guardians'] = $ret;
    api_exit($result);
}