<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

$name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : null;
$province = isset($_GET['province']) && !empty($_GET['province']) ? $_GET['province'] : null;
$city = isset($_GET['city']) && !empty($_GET['city']) ? $_GET['city'] : null;
$county = isset($_GET['county']) && !empty($_GET['county']) ? $_GET['county'] : null;
$user = isset($_GET['user']) && !empty($_GET['user']) ? $_GET['user'] : null;
$intension = isset($_GET['intension']) && !empty($_GET['intension']) ? $_GET['intension'] : null;
$type = isset($_GET['type']) && !empty($_GET['type']) ? $_GET['type'] : '';

$ret = DbiSale::getDbi()->getAgencyList($name, $province, $city, $county, $user, $intension, $type);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['list'] = $ret;
    api_exit($result);
}
