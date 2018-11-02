<?php
require_once PATH_LIB . 'db/DbiEcgn.php';

$name = isset($_GET['name']) ? $_GET['name'] : null;

$ret = DbiEcgn::getDbi()->getRoom($name);
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
