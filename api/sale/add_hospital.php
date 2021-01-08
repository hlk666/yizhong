<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_name.']);
}
/*
if (false === Validate::checkRequired($_POST['hospital_contact'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_contact.']);
}
if (false === Validate::checkRequired($_POST['hospital_tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_tel.']);
}
if (false === Validate::checkRequired($_POST['province'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'province.']);
}
if (false === Validate::checkRequired($_POST['city'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'city.']);
}
if (false === Validate::checkRequired($_POST['county'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'county.']);
}
if (false === Validate::checkRequired($_POST['intension'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'intension.']);
}
$agencyId = isset($_POST['agency_id']) && !empty($_POST['agency_id']) ? $_POST['agency_id'] : '0';
*/
/*
$isExisted = DbiSale::getDbi()->existedHospital($_POST['name'], $_POST['tel']);
if (VALUE_DB_ERROR === $isExisted) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (true === $isExisted) {
    api_exit(['code' => '3', 'message' => '该医院已存在。']);
}
*/
/*
$ret = DbiSale::getDbi()->addHospital($_POST['hospital_name'], $_POST['hospital_contact'], $_POST['hospital_tel'], 
        $_POST['province'], $_POST['city'], $_POST['county'], $agencyId, $_POST['intension']);
*/
$data = $_POST;
unset($data['hospital_name']);
$ret = DbiSale::getDbi()->addHospital($_POST['hospital_name'], $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (isset($_POST['operator']) && !empty($_POST['operator'])) {
    $file = PATH_DATA . 'sale_operation.txt';
    file_put_contents($file, 'hospital,' . $ret . ',' . $_POST['operator'] . ';', FILE_APPEND);
}

api_exit_success();
