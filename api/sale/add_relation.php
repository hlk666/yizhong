<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['agency_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'agency_id.']);
}

$createTime = isset($_POST['create_time']) && !empty($_POST['create_time']) ? $_POST['create_time'] : date('Y-m-d H:i:s');
$content = isset($_POST['content']) && !empty($_POST['content']) ? $_POST['content'] : '';
$bidTimes = isset($_POST['bid_times']) && !empty($_POST['bid_times']) ? $_POST['bid_times'] : '';
$source = isset($_POST['source']) && !empty($_POST['source']) ? $_POST['source'] : '';

$ret = DbiSale::getDbi()->addRelation($_POST['hospital_id'], $_POST['agency_id'], 
        $createTime, $content, $bidTimes, $source);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
