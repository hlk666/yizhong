<?php
require_once PATH_LIB . 'DbiAdmin.php';

$province = isset($_GET['province']) && !empty($_GET['province']) ? $_GET['province'] : null;

$list = DbiAdmin::getDbi()->getHospitalArea($province);
if (VALUE_DB_ERROR === $list) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($list)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'list' => $list]);
