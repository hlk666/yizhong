<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'id.']);
}
if (false === Validate::checkRequired($_GET['table'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'table.']);
}

$ret = DbiSale::getDbi()->getInfoOne($_GET['table'], $_GET['id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['data'] = [$ret];
    api_exit($result);
}
