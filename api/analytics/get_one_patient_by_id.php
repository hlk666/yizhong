<?php
require PATH_LIB . 'DbiAnalytics.php';

if (empty($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$patient = DbiAnalytics::getDbi()->getPatientOneData($_GET['patient_id']);
if (VALUE_DB_ERROR === $patient) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$patient['code'] = 0;
api_exit($patient);
