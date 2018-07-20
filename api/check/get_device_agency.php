<?php
require_once PATH_LIB . 'DbiAdmin.php';

$agency = isset($_GET['agency']) ? $_GET['agency'] : '';
$salesman = isset($_GET['salesman']) ? $_GET['salesman'] : '';
$ret = DbiAdmin::getDbi()->getDeviceAgency($agency, $salesman);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['devices'] = $ret;
    api_exit($result);
}
