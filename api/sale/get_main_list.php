<?php
require_once PATH_LIB . 'db/DbiSale.php';

$hospital = DbiSale::getDbi()->getMainList('hospital');
if (VALUE_DB_ERROR === $hospital) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$agency = DbiSale::getDbi()->getMainList('agency');
if (VALUE_DB_ERROR === $agency) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$user = DbiSale::getDbi()->getMainList('user');
if (VALUE_DB_ERROR === $user) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['hospital'] = $hospital;
$result['agency'] = $agency;
$result['user'] = $user;
api_exit($result);
