<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
}
if (false === Validate::checkRequired($_POST['sex'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'sex.']);
}
if (false === Validate::checkRequired($_POST['birth_year'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'birth_year.']);
}
if (false === Validate::checkRequired($_POST['tel'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
}

if (false === Validate::checkRequired($_POST['department_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'department_id.']);
}
if (false === Validate::checkRequired($_POST['exam_name'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'exam_name.']);
}
if (false === Validate::checkRequired($_POST['doctor_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_id.']);
}

$caseId = isset($_POST['case_id']) ? $_POST['case_id'] : '';
$hospitalizationId = isset($_POST['hospitalization_id']) ? $_POST['hospitalization_id'] : '';
$outpatientId = isset($_POST['outpatient_id']) ? $_POST['outpatient_id'] : '';
$medicalInsuranceId = isset($_POST['medical_insurance']) ? $_POST['medical_insurance'] : '';

$examinationId = DbiEcgn::getDbi()->apply($_POST['name'], $_POST['sex'], $_POST['birth_year'], 
        $_POST['tel'], $_POST['department_id'], $_POST['exam_name'], $_POST['doctor_id'],
        $caseId, $hospitalizationId, $outpatientId, $medicalInsuranceId);
if (VALUE_DB_ERROR === $examinationId) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();

