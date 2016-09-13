<?php
require PATH_ROOT . 'lib/tool/HpPatientDiagnosis.php';

if (empty($_GET['diagnosis'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'diagnosis.']);
}

$diagnosisList = $_GET['diagnosis'];
$result = HpPatientDiagnosis::getPatientsByDiagnosis($diagnosisList);

api_exit($result);
