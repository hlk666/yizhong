<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['agency_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'agency_id.']);
}
if ("0" == $_GET['account_id']) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'account_id.']);
}


$agencyId = $_GET['agency_id'];
$ret = Dbi::getDbi()->getAgencyHospitals($agencyId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['hospitals'] = $ret;
    api_exit($result);
}