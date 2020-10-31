<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['county_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'county_id.']);
}

$isExisted = DbiSale::getDbi()->existedId('county', $_POST['county_id']);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true === $isExisted) {
    api_exit(['code' => '3', 'message' => '该县数据已存在。']);
}

$combination = isset($_POST['combination']) && !empty($_POST['combination']) ? $_POST['combination'] : '';
$content = isset($_POST['content']) && !empty($_POST['content']) ? $_POST['content'] : '';
$agencyId = isset($_POST['agency_id']) && !empty($_POST['agency_id']) ? $_POST['agency_id'] : '0';

$ret = DbiSale::getDbi()->addCounty($_POST['county_id'], $combination, $content, $agencyId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
