<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['start_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'start_time.']);
}
if (false === Validate::checkRequired($_GET['end_time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'end_time.']);
}

$startTime = $_GET['start_time'];
$endTime = $_GET['end_time'];

if (isset($_GET['doctor_id'])) {
    if (false === Validate::checkRequired($_GET['doctor_id'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id | hospital_id.']);
    }
    $doctorList = $_GET['doctor_id'];
} else {
    if (false === Validate::checkRequired($_GET['hospital_id'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id | hospital_id.']);
    }
    $hospital = $_GET['hospital_id'];
    $accountList = DbiAdmin::getDbi()->getAccountList($hospital);
    if (VALUE_DB_ERROR === $accountList) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (empty($accountList)) {
        api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
    }
    $doctorList = '';
    foreach ($accountList as $account) {
        $doctorList .= $account['doctor_id'] . ',';
    }
    $doctorList = substr($doctorList, 0, -1);
}

$patients = DbiAdmin::getDbi()->getAccountForAnalytics($doctorList, $startTime, $endTime);
if (VALUE_DB_ERROR === $patients) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($patients)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'patients' => $patients]);
