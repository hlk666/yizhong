<?php
require PATH_LIB . 'DbiERP.php';

$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'erp' . DIRECTORY_SEPARATOR . 'changed_hospital.txt';


$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;

if (file_exists($file)) {
    $ret = DbiERP::getDbi()->getHospitalInfoList(file_get_contents($file));
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    $result['list'] = $ret;
} else {
    $result['list'] = array();
}

api_exit($result);
