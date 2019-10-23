<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

$xml = '<OriginalSummary>
                <PatientInfo>
                    <Create_by>未知</Create_by>
                    <InPatientNo>1004898</InPatientNo>
                    <PatientAge>68</PatientAge>
                    <PatientSex>男</PatientSex>
                    <Patient_IDCard>321102195003290039</Patient_IDCard>
                    <StudyID>1654</StudyID>
                    <create_date>2019-07-11 17:50:46</create_date>
                    <patientID>433</patientID>
                    <patientName>仲正森</patientName>
                    <patient_age_unit>岁</patient_age_unit>
                    <patient_tel>13871197229</patient_tel>
                </PatientInfo>
                <RecordInfo>
                    <Company>麦迪克斯</Company>
                    <Department>C区住院部</Department>
                    <FormatNO>0000</FormatNO>
                    <HospitalAreaName>鼓楼区</HospitalAreaName>
                    <HospitalName>南京大学医学院附属鼓楼医院</HospitalName>
                    <LeadType>1</LeadType>
                    <PowerFrequencyFilter>50HZ</PowerFrequencyFilter>
                    <RecorderModel>MHR110</RecorderModel>
                    <RecorderNO>C341701003</RecorderNO>
                    <RecorderType>2</RecorderType>
                    <RegisterDate>2019-07-11 17:50:46</RegisterDate>
                    <RegisterUser>何医生</RegisterUser>
                    <VaildTime>24</VaildTime>
                </RecordInfo>
                <StudyInfo>
                    <AppHospitalName>南京大学医学院附属鼓楼医院</AppHospitalName>
                    <BuildTime>2019-07-11 17:50:46</BuildTime>
                    <DepartmentContact>何医生</DepartmentContact>
                    <DepartmentContactTel>010-12345678</DepartmentContactTel>
                    <HospitalAreaName>鼓楼区</HospitalAreaName>
                    <HospitalName>南京大学医学院附属鼓楼医院</HospitalName>
                    <InPatientNo>1004898</InPatientNo>
                    <JianChayishi>何医生</JianChayishi>
                    <OperatingPhysician>何医生</OperatingPhysician>
                    <RecordNo>C341701003</RecordNo>
                    <RecorderNO>C341701003</RecorderNO>
                    <SeriesDate>2019-07-11</SeriesDate>
                    <SeriesTime>17:50:46</SeriesTime>
                    <StudyDate>2019-07-11</StudyDate>
                    <StudyDept>神经外科</StudyDept>
                    <StudyID>1654</StudyID>
                    <StudyInstanceUID>0fb310f4-a3c1-11e9-8573-54bf643dd2d2</StudyInstanceUID>
                    <StudyRoom>神经外科</StudyRoom>
                    <bingqu>神经外科一病区</bingqu>
                    <chuanghao>17床</chuanghao>
                    <department>B区住院部</department>
                    <patientID>433</patientID>
                    <shenqingYiShi>胡卫星</shenqingYiShi>
                </StudyInfo>
            </OriginalSummary>';
if (false === Validate::checkRequired($_POST['InMessage'])) {
    api_exit(['ResultCode' => '0', 'ResultInfo' => MESSAGE_PARAM]);
}
$xml = $_POST['InMessage'];
$param = XML::getXml($xml);

if (empty($param) || !isset($param['PatientInfo']) || !isset($param['StudyInfo']) || !isset($param['RecordInfo'])) {
    $resultInfo = MESSAGE_PARAM;
} elseif (false === Validate::checkRequired($param['PatientInfo']['patientID'])) {
    $resultInfo = '参数不足：patientID.';
} elseif (false === Validate::checkRequired($param['StudyInfo']['StudyInstanceUID'])) {
    $resultInfo = '参数不足：StudyInstanceUID.';
} elseif (false === Validate::checkRequired($param['RecordInfo']['RecorderNO'])) {
    $resultInfo = '参数不足：RecorderNO.';
} elseif (Dbi::getDbi()->existedBind($param['StudyInfo']['RecorderNO'])) {
    $resultInfo = '设备' . $param['StudyInfo']['RecorderNO'] . '尚未解除绑定。';
} elseif (Dbi::getDbi()->existedStudy($param['StudyInfo']['StudyInstanceUID'])) {
    $resultInfo = "该病历数据已存在。";
} else {
    $resultInfo = '';
}
if (!empty($resultInfo)) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => $resultInfo]);
}

$ret = Dbi::getDbi()->bind($param['PatientInfo']['patientID'], $param['PatientInfo']['patientName'], 
        $param['PatientInfo']['PatientSex'], $param['PatientInfo']['PatientBirthday'], $param['PatientInfo']['PatientAge'], 
        $param['PatientInfo']['patient_age_unit'], $param['PatientInfo']['patient_tel'], 
        $param['PatientInfo']['EmergencyContact'],  $param['PatientInfo']['EmergencyContactTel'], 
        $param['PatientInfo']['OutPatientNo'], $param['PatientInfo']['InPatientNo'],
        $param['PatientInfo']['InsuranceType'], $param['PatientInfo']['CaseNO'], $param['PatientInfo']['StudyID'],
        $param['PatientInfo']['Patient_IDCard'], $param['PatientInfo']['patient_stature'], $param['PatientInfo']['patient_weight'],
        $param['PatientInfo']['patient_address'], $param['PatientInfo']['Create_by'], $param['PatientInfo']['create_date'],
        $param['RecordInfo']['RecorderNO'], $param['RecordInfo']['RecorderModel'], $param['RecordInfo']['LeadType'],
        $param['RecordInfo']['PowerFrequencyFilter'], $param['RecordInfo']['VaildTime'], $param['RecordInfo']['Company'],
        $param['RecordInfo']['Department'], $param['RecordInfo']['HospitalName'], $param['RecordInfo']['HospitalAreaName'],
        $param['RecordInfo']['RegisterDate'], $param['RecordInfo']['RegisterUser'], $param['RecordInfo']['FormatNO'],
        $param['RecordInfo']['RecorderType'], 
        $param['StudyInfo']['StudyInstanceUID'], $param['StudyInfo']['StudyDate'], $param['StudyInfo']['AccessionNumber'],
        $param['StudyInfo']['OperatingPhysician'], $param['StudyInfo']['StudyInformation'], $param['StudyInfo']['StudyID'],
        $param['StudyInfo']['department'], $param['StudyInfo']['bingqu'], $param['StudyInfo']['fanghao'],
        $param['StudyInfo']['chuanghao'], $param['StudyInfo']['shoujianyanyin'], $param['StudyInfo']['linchuangzhenduan'],
        $param['StudyInfo']['laiyuan'], $param['StudyInfo']['InvoiceNo'], $param['StudyInfo']['shenqingMudi'],
        $param['StudyInfo']['SeriesDate'], $param['StudyInfo']['SeriesTime'], $param['StudyInfo']['StudyDept'],
        $param['StudyInfo']['StudyRoom'], $param['StudyInfo']['JianChayishi'], $param['StudyInfo']['shenqingYiShi'],
        $param['StudyInfo']['HospitalName'], $param['StudyInfo']['AppHospitalName'], $param['StudyInfo']['HospitalAreaName'],
        $param['StudyInfo']['DepartmentContact'], $param['StudyInfo']['DepartmentContactTel'], $param['StudyInfo']['BuildTime']);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['ResultCode' => '0', 'OutData' => '', 'ResultInfo' => MESSAGE_DB_ERROR]);
}

api_exit(['ResultCode' => '1', 'OutData' => '', 'ResultInfo' => MESSAGE_SUCCESS]);
