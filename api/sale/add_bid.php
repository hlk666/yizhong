<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['agency_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'agency_id.']);
}
/*
if (false === Validate::checkRequired($_POST['product'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'product.']);
}
if (false === Validate::checkRequired($_POST['amount'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'amount.']);
}
if (false === Validate::checkRequired($_POST['bid_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'bid_time.']);
}
if (false === Validate::checkRequired($_POST['content'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'content.']);
}
*/

$product = isset($_POST['product']) && !empty($_POST['product']) ? $_POST['product'] : '';
$amount = isset($_POST['amount']) && !empty($_POST['amount']) ? $_POST['amount'] : '';
$bidTime = isset($_POST['bid_time']) && !empty($_POST['bid_time']) ? $_POST['bid_time'] : '';
$content = isset($_POST['content']) && !empty($_POST['content']) ? $_POST['content'] : '';
$source = isset($_POST['source']) && !empty($_POST['source']) ? $_POST['source'] : '';
$level = isset($_POST['level']) && !empty($_POST['level']) ? $_POST['level'] : '0';

$ret = DbiSale::getDbi()->addBid($_POST['hospital_id'], $_POST['agency_id'], 
        $product, $amount, $bidTime, $content, $source, $level);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
