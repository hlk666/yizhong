<?php
require PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';

$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'erp' . DIRECTORY_SEPARATOR . 'changed_hospital.txt';


$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;


if (file_exists($file)) {
    $result['list'] = file_get_contents($file);
} else {
    $result['list'] = '';
}

api_exit($result);
