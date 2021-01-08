<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

$name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : null;
$province = isset($_GET['province']) && !empty($_GET['province']) ? $_GET['province'] : null;
$city = isset($_GET['city']) && !empty($_GET['city']) ? $_GET['city'] : null;
$county = isset($_GET['county']) && !empty($_GET['county']) ? $_GET['county'] : null;
$agency = isset($_GET['agency']) && !empty($_GET['agency']) ? $_GET['agency'] : null;
$user = isset($_GET['user']) && !empty($_GET['user']) ? $_GET['user'] : null;
$successRate = isset($_GET['success_rate']) && !empty($_GET['success_rate']) ? $_GET['success_rate'] : null;

$ret = DbiSale::getDbi()->getHospitalList($name, $province, $city, $county, $agency, $user, $successRate);
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
