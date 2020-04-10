<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

/*
if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
*/

$list = DbiAdmin::getDbi()->getCloudData();
if (VALUE_DB_ERROR === $list) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($list)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}
$total = 0;
$notUpload = 0;
$uploaded = 0;
$reporting = 0;
$reported = 0;
$troubled = 0;
$other = 0;
foreach ($list as $item) {
    $total++;
    if ($item['status'] < 2) {
        $notUpload++;
    } elseif ($item['status'] == 2) {
        $uploaded++;
    } elseif ($item['status'] == 3 || $item['status'] == 6) {
        $reporting++;
    } elseif ($item['status'] == 4 || $item['status'] == 5) {
        $reported++;
    } elseif ($item['status'] == 7) {
        $troubled++;
    } else {
        $other++;
    }
}

$ret = array();
$ret['code'] = '0';
$ret['message'] = MESSAGE_SUCCESS;
$ret['list'] = $list;
$ret['total'] = $total;
$ret['not_upload'] = $notUpload;
$ret['uploaded'] = $uploaded;
$ret['reporting'] = $reporting;
$ret['reported'] = $reported;
$ret['troubled'] = $troubled;
$ret['other'] = $other;

api_exit($ret);
