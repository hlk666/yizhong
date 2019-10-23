<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

$xml = '<?xml version="1.0" encoding="GB2312"?>
<OriginalSummary>
<StudyInstanceUID>8adf67aa-a903-11e9-91dd-0242ac110005</StudyInstanceUID>
</OriginalSummary>';

if (false === Validate::checkRequired($_POST['InMessage'])) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_PARAM]);
}
$xml = $_POST['InMessage'];

$param = XML::getXml($xml);

if (empty($param)) {
    $resultInfo = MESSAGE_PARAM;
} elseif (false === Validate::checkRequired($param['StudyInstanceUID'])) {
    $resultInfo = '参数不足：StudyInstanceUID.';
} elseif (!Dbi::getDbi()->existedStudy($param['StudyInstanceUID'])) {
    $resultInfo = "不存在该病历数据。";
} else {
    $resultInfo = '';
}
if (!empty($resultInfo)) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => $resultInfo]);
}

if (!Dbi::getDbi()->existedStudyReported($param['StudyInstanceUID'])) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => '该病历数据未审核完毕。']);
}

$ret = Dbi::getDbi()->getStudyReported($param['StudyInstanceUID']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => MESSAGE_DB_ERROR]);
}
if (empty($ret)) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => MESSAGE_DB_NO_DATA]);
}
$patientInfo = array('PatientName' => $ret['patient_name'], 'PatientSex' => $ret['patient_sex'], 
                'PatientAge' => $ret['patient_age'], 'PatientBirthday' => $ret['patient_birthday']);
$studyInfo = array('StudyUID' => $ret['study_instance_uid'], 'bingqu' => $ret['bingqu'], 
                'chuanghao' => $ret['chuanghao'], 'InPatientNo' => $ret['inpatient_no'],
                'OutPatientNo' => $ret['outpatient_no'], 'HOSPITALNAME' => $ret['hospital_name'],
                'shenqingYiShi' => $ret['shenqingyishi'], 'department' => $ret['department'],
                'StudyStartDate' => $ret['start_time'], 'StudyEndDate' => $ret['end_time'],
                'JianChayishi' => $ret['jianchayishi'], 'ReportingDate' => $ret['report_date'],
                'ReportingPhysician' => $ret['report_physician'], 'ReferringDate' => $ret['refer_date'],
                'ReferringPhysician' => $ret['refer_physician'], 
                'linchuangzhenduan' => $ret['linchuangzhenduan'],
                'jianchajielun' => $ret['jianchajielun']
);

api_exit(['ResultCode' => '1', 'OutData' => ['PatientInfo' => $patientInfo, 'StudyInfo' => $studyInfo], 
                'ResultInfo' => MESSAGE_SUCCESS]);
