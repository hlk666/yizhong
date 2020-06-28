<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$hospitalId = $_GET['hospital_id'];

$file = PATH_DATA . 'hospital_child' . DIRECTORY_SEPARATOR . $hospitalId . '.txt';
$ret = array();
if (file_exists($file)) {
    $text = file_get_contents($file);
    $items = explode(';', $text);
    foreach ($items as $item) {
        $detail = explode(',', $item);
        if (!empty($detail) && isset($detail[1])) {
            $ret[] = ['hospital_id' => $detail[0], 'hospital_name' => $detail[1]];
        }
    }
}
/*
$ret = Dbi::getDbi()->getHospitalChild($hospitalId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
*/
if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['hospitals'] = $ret;
    api_exit($result);
}