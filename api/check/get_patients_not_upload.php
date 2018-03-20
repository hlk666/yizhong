<?php
require_once PATH_LIB . 'DbiAdmin.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['time'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'time.']);
}

$patients = DbiAdmin::getDbi()->getPatientNotUpload($_GET['time']);
if (VALUE_DB_ERROR === $patients) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($patients)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}

api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS, 'patients' => $patients]);
