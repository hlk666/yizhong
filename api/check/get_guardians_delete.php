<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

$sTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$eTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;
$ret = DbiAdmin::getDbi()->getGuardiansDelete($sTime, $eTime);
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
