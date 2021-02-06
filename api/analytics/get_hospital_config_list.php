<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['hospitals'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospitals.']);
}
//$hospitals = $_GET['hospitals'];
$hospitals = str_replace(',,', ',', $_GET['hospitals'])

$hospitalConfig = DbiAnalytics::getDbi()->getHospitalConfigList($hospitals);
if (VALUE_DB_ERROR === $hospitalConfig) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($hospitalConfig)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
} else {
    foreach ($hospitalConfig as $key => $value) {
        if (strpos($value['title_hospital_name'], '签名用') !== false) {
            $hospitalConfig[$key]['title_hospital_name'] = str_replace('(签名用)', '', $value['title_hospital_name']);
        }
    }
    
    $ret['code'] = '0';
    $ret['message'] = MESSAGE_SUCCESS;
    $ret['list'] = $hospitalConfig;
}
api_exit($ret);