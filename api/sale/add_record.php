<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

/*
if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['agency_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'agency_id.']);
}*/
if (false === Validate::checkRequired($_POST['user_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'user_id.']);
}
if (false === Validate::checkRequired($_POST['record_text'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'record_text.']);
}

$agencyId = isset($_POST['agency_id']) && !empty($_POST['agency_id']) ? $_POST['agency_id'] : '0';
$hospitalId = isset($_POST['hospital_id']) && !empty($_POST['hospital_id']) ? $_POST['hospital_id'] : '0';
$recordTime = isset($_POST['record_time']) && !empty($_POST['record_time']) ? $_POST['record_time'] : date('Y-m-d H:i:s');
$yuanzhang = isset($_POST['yuanzhang']) && !empty($_POST['yuanzhang']) ? $_POST['yuanzhang'] : '0';
$fenguanyuanzhang = isset($_POST['fenguanyuanzhang']) && !empty($_POST['fenguanyuanzhang']) ? $_POST['fenguanyuanzhang'] : '0';
$xinneike = isset($_POST['xinneike']) && !empty($_POST['xinneike']) ? $_POST['xinneike'] : '0';
$xindiantushi = isset($_POST['xindiantushi']) && !empty($_POST['xindiantushi']) ? $_POST['xindiantushi'] : '0';
$style = isset($_POST['style']) && !empty($_POST['style']) ? $_POST['style'] : '';
$planId= isset($_POST['plan_id']) ? $_POST['plan_id'] : '0';

$ret = DbiSale::getDbi()->addRecord($_POST['user_id'], $_POST['record_text'], $hospitalId, $agencyId, $recordTime, 
        $yuanzhang, $fenguanyuanzhang, $xinneike, $xindiantushi, $style, $planId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
