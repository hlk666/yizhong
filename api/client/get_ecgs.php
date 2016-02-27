<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
$guardianId = $_GET['patient_id'];
$readStatus = isset($_GET['read_status']) ? $_GET['read_status'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$rows = isset($_GET['rows']) ? $_GET['rows'] : VALUE_DEFAULT_ROWS;
$offset = $page * $rows;

$ret = Dbi::getDbi()->getEcgs($guardianId, $offset, $rows, $readStatus);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['ecgs'] = $ret;
    
    $file = PATH_CACHE_CMD . $guardianId . '.php';
    if (file_exists($file)) {
        include $file;
        $result['mode2_lead'] = $info['mode2_lead'];
        $result['mode3_lead'] = $info['mode3_lead'];
    } else {
        $result['mode2_lead'] = '';
        $result['mode3_lead'] = '';
    }
    
    api_exit($result);
}
