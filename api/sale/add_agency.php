<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

/*
if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}*/
if (false === Validate::checkRequired($_POST['contact'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'contact.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}
/*
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
if (false === Validate::checkRequired($_POST['intension'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'intension.']);
}
*/
$name = isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : '';
$status = isset($_POST['status']) && !empty($_POST['status']) ? $_POST['status'] : '0';
$agencyProvince = isset($_POST['agency_province']) && !empty($_POST['agency_province']) ? $_POST['agency_province'] : '';
$agencyCity = isset($_POST['agency_city']) && !empty($_POST['agency_city']) ? $_POST['agency_city'] : '';
$agencyCounty = isset($_POST['agency_county']) && !empty($_POST['agency_county']) ? $_POST['agency_county'] : '';
$address = isset($_POST['address']) && !empty($_POST['address']) ? $_POST['address'] : '';
$type = isset($_POST['type']) && !empty($_POST['type']) ? $_POST['type'] : '0';
$intension = isset($_POST['intension']) && !empty($_POST['intension']) ? $_POST['intension'] : '';
$totalBidTimes = isset($_POST['total_bid_times']) && !empty($_POST['total_bid_times']) ? $_POST['total_bid_times'] : '0';
$content = isset($_POST['content']) && !empty($_POST['content']) ? $_POST['content'] : '';
$source = isset($_POST['source']) && !empty($_POST['source']) ? $_POST['source'] : '';
$userId = isset($_POST['user_id']) && !empty($_POST['user_id']) ? $_POST['user_id'] : '';

$isExisted = DbiSale::getDbi()->existedAgency($_POST['name'], $_POST['tel']);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true === $isExisted) {
    api_exit(['code' => '3', 'message' => '该代理商已存在。']);
}

$ret = DbiSale::getDbi()->addAgency($name, $_POST['contact'], $_POST['tel'], $status, 
        $agencyProvince, $agencyCity, $agencyCounty, $address, $type, $intension, $content, $source, $userId, $totalBidTimes);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (isset($_POST['operator']) && !empty($_POST['operator'])) {
    $file = PATH_DATA . 'sale_operation.txt';
    file_put_contents($file, 'agency,' . $ret . ',' . $_POST['operator'] . ';', FILE_APPEND);
}

api_exit_success();
