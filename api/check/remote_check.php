<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';

if (false === Validate::checkRequired($_GET['patients'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patients.']);
}

$list = explode(',', $_GET['patients']);
if (empty($list) || empty($list[0])) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}

$result = array();
$data = array('check_info' => 'on');
foreach ($list as $guardianId) {
    $invigilator = new Invigilator($guardianId);
    $ret = $invigilator->create($data);
    
    if (VALUE_PARAM_ERROR === $ret) {
        $temp = [$guardianId => '1'];
    } elseif (VALUE_GT_ERROR === $ret) {
        $temp = [$guardianId => '2'];
    } else {
        $temp = [$guardianId => '0'];
    }
    
    $result[] = $temp;
}

api_exit(['list' => $result]);
