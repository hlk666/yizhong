<?php
require_once PATH_LIB . 'db/DbiSale.php';
require_once PATH_LIB . 'Validate.php';

$hospitalId = isset($_POST['hospital_id']) && !empty($_POST['hospital_id']) ? $_POST['hospital_id'] : '';
$agencyId = isset($_POST['agency_id']) && !empty($_POST['agency_id']) ? $_POST['agency_id'] : '';
$planText = isset($_POST['plan_text']) && !empty($_POST['plan_text']) ? $_POST['plan_text'] : '';
$deadLine = isset($_POST['dead_line']) && !empty($_POST['dead_line']) ? $_POST['dead_line'] : '';
$userId= isset($_POST['user_id']) && !empty($_POST['user_id']) ? $_POST['user_id'] : '';


$ret = DbiSale::getDbi()->addPlan($hospitalId, $agencyId, $planText, $deadLine, $userId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
