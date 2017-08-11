<?php
require_once PATH_LIB . 'DbiAdmin.php';

$salesman = isset($_GET['salesman']) && !empty($_GET['salesman']) ? $_GET['salesman'] : null;

$ret = DbiAdmin::getDbi()->getHospitalSalesman($salesman);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['salesman_hospital'] = $ret;
    api_exit($result);
}
