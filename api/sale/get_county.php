<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

$county = isset($_GET['county']) && !empty($_GET['county']) ? $_GET['county'] : null;
$agencyId = isset($_GET['agency_id']) && !empty($_GET['agency_id']) ? $_GET['agency_id'] : null;

$ret = DbiSale::getDbi()->getCounty($county, $agencyId);
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
