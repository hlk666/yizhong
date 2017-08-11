<?php
require_once PATH_LIB . 'DbiAdmin.php';

$agency = isset($_GET['agency']) && !empty($_GET['agency']) ? $_GET['agency'] : null;

$ret = DbiAdmin::getDbi()->getHospitalAgency($agency);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['agency_hospital'] = $ret;
    api_exit($result);
}
