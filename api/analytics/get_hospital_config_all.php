<?php
require_once PATH_LIB . 'DbiAnalytics.php';


$configs = DbiAnalytics::getDbi()->getHospitalConfigAll();
if (VALUE_DB_ERROR === $configs) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$ret['code'] = '0';
$ret['message'] = MESSAGE_SUCCESS;
$ret['config'] = $configs;
api_exit($ret);
