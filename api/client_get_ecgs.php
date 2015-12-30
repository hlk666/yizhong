<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
$guardianId = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$readStatus = isset($_GET['read_status']) ? $_GET['read_status'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$rows = isset($_GET['rows']) ? $_GET['rows'] : VALUE_DEFAULT_ROWS;
$offset = $page * $rows;

$ret = Dbi::getDbi()->getEcgs($guardianId, $offset, $rows, $readStatus);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '3', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = '';
    
    $result['ecgs'] = $ret;
    api_exit($result);
}
