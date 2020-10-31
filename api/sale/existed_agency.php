<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}

$name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : '';

$isExisted = DbiSale::getDbi()->existedAgency($name, $_POST['tel']);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['is_existed'] = $isExisted;
api_exit($result);
