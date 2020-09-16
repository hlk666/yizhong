<?php
require_once PATH . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['contact'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'contact.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}
/*
if (false === Validate::checkRequired($_POST['target_hospital'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'target_hospital.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
if (false === Validate::checkRequired($_POST['intension'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'intension.']);
}
*/
$agencyProvince = isset($_POST['agency_province']) && !empty($_POST['agency_province']) ? $_POST['agency_province'] : '';
$agencyCity = isset($_POST['agency_city']) && !empty($_POST['agency_city']) ? $_POST['agency_city'] : '';
$agencyCounty = isset($_POST['agency_county']) && !empty($_POST['agency_county']) ? $_POST['agency_county'] : '';
$type = isset($_POST['type']) && !empty($_POST['type']) ? $_POST['type'] : '';
$intension = isset($_POST['intension']) && !empty($_POST['intension']) ? $_POST['intension'] : '';

$isExisted = DbiSale::getDbi()->existedAgency($_POST['name'], $_POST['tel']);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true === $isExisted) {
    api_exit(['code' => '3', 'message' => '该代理商已存在。']);
}

$ret = DbiSale::getDbi()->addAgency($_POST['name'], $_POST['contact'], $_POST['tel'], 
        $agencyProvince, $agencyCity, $agencyCity, $type, $intension);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
