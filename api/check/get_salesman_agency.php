<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['salesman_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'salesman_id.']);
}

$ret = DbiAdmin::getDbi()->getSalesmanAgency($_GET['salesman_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['list'] = $ret;
    api_exit($result);
}