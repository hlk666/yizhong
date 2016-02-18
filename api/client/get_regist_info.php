<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_GET['patient_id'];

$ret = Dbi::getDbi()->getRegistInfo($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    
    $ret['age'] = (string)(date('Y') - $ret['birth_year']);
    $ret['sex'] = $ret['sex'] == 1 ? '男' : '女';
    unset($ret['birth_year']);
    
    api_exit(array_merge($result, $ret));
}
