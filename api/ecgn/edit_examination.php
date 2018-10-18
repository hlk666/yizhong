<?php
require_once PATH_LIB . 'db/DbiEcgn.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['examination_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'examination_id.']);
}

$ret = DbiEcgn::getDbi()->existedExamination($_POST['examination_id']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (false === $ret) {
    api_exit(['code' => '18', 'message' => '对象不存在。']);
}

$caseId = isset($_POST['case_id']) ? $_POST['case_id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;
$examName = isset($_POST['exam_name']) ? $_POST['exam_name'] : null;
$applyDepartmentId = isset($_POST['apply_department_id']) ? $_POST['apply_department_id'] : null;
$applyDoctorId = isset($_POST['apply_doctor_id']) ? $_POST['apply_doctor_id'] : null;
$examDepartmentId = isset($_POST['exam_department_id']) ? $_POST['exam_department_id'] : null;
$examDoctorId = isset($_POST['exam_doctor_id']) ? $_POST['exam_doctor_id'] : null;
$roomId = isset($_POST['room_id']) ? $_POST['room_id'] : null;
$diagnosisDoctorId = isset($_POST['diagnosis_doctor_id']) ? $_POST['diagnosis_doctor_id'] : null;
$diagnosisText = isset($_POST['diagnosis_text']) ? $_POST['diagnosis_text'] : null;
$outpatientId = isset($_POST['outpatient_id']) ? $_POST['outpatient_id'] : null;
$hospitalizationId = isset($_POST['hospitalization_id']) ? $_POST['hospitalization_id'] : null;
$medicalInsurance = isset($_POST['medical_insurance']) ? $_POST['medical_insurance'] : null;

$data = array();
if (null !== $caseId) {
    $data['case_id'] = $caseId;
}
if (null !== $status) {
    $data['status'] = $status;
}
if (null !== $examName) {
    $data['exam_name'] = $examName;
}
if (null !== $applyDepartmentId) {
    $data['apply_department_id'] = $applyDepartmentId;
}
if (null !== $applyDoctorId) {
    $data['apply_doctor_id'] = $applyDoctorId;
}
if (null !== $examDepartmentId) {
    $data['exam_department_id'] = $examDepartmentId;
}
if (null !== $examDoctorId) {
    $data['exam_doctor_id'] = $examDoctorId;
}
if (null !== $roomId) {
    $data['room_id'] = $roomId;
}
if (null !== $diagnosisDoctorId) {
    $data['diagnosis_doctor_id'] = $diagnosisDoctorId;
}
if (null !== $diagnosisText) {
    $data['diagnosis_text'] = $diagnosisText;
}
if (null !== $outpatientId) {
    $data['outpatient_id'] = $outpatientId;
}
if (null !== $hospitalizationId) {
    $data['hospitalization_id'] = $hospitalizationId;
}
if (null !== $medicalInsurance) {
    $data['medical_insurance'] = $medicalInsurance;
}

if (empty($data)) {
    api_exit(['code' => '1', 'message' => '没有修改任何信息。']);
}

$ret = DbiEcgn::getDbi()->editExamination($_POST['examination_id'], $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

api_exit_success();
