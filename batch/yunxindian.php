<?php
require 'common.php';
require_once PATH_LIB . 'DbiAnalytics.php';

$guardianId = $_GET['id'];
if (empty($guardianId)) {
    echo 'param error.';
    exit(-1);
}

$patient = DbiAnalytics::getDbi()->getPatientForHenan($guardianId);
if (VALUE_DB_ERROR === $patient) {
    Logger::write('henan_agency.log', 'db error : ' . $guardianId);
    exit(0);
} elseif (empty($patient)) {
    Logger::write('henan_agency.log', 'no patient : ' . $guardianId);
    exit(0);
} else {
    //do nothing.
}

if ($patient['agency_id'] != 113) {
    echo 'agency error.';
    exit(-1);
}

Logger::write('henan_agency.log', 'start: ' . $guardianId);

if ($patient['regist_hospital_id'] == 746) {
    $client = new SoapClient('http://120.194.75.148:8008/services/apiservice.asmx?WSDL');
} elseif ($patient['regist_hospital_id'] == 802) {
    $client = new SoapClient('http://218.28.211.1:8008/services/apiservice.asmx?WSDL');
} elseif ($patient['regist_hospital_id'] == 793) {
    $client = new SoapClient('http://117.158.59.210:8008/services/apiservice.asmx?WSDL');
} else {
    $client = new SoapClient('http://holter.hnecg.com/services/apiservice.asmx?WSDL');
}
if ($client) {
    $client->soap_defencoding = 'utf-8';
    $client->decode_utf8 = false;
    $client->xml_encoding = 'utf-8';
    
    $param = array();
    $param['id'] = $guardianId;
    $param['patientName'] = $patient['name'];
    $param['gender'] = ($patient['sex'] == '1') ? true : false;
    $param['patientId'] = '';
    $param['cardNo'] = '';
    $param['bedNo'] = '';
    $param['outpatientNo'] = '';
    $param['inpatientNo'] = '';
    $param['pacemaker'] = false;
    $param['age'] = date('Y') - $patient['birth_year'];
    $param['ageUnit'] = 'Y';
    $param['applyNo'] = '';
    $param['dataSource'] = '';
    $param['applyDept'] = '';
    $param['applyDoctor'] = '';
    $param['applyDate'] = $patient['end_time'];
    $param['telephone'] = '';
    $param['operatorName'] = '';
    $param['recordDate'] = $patient['start_time'];
    $param['clinicDiag'] = '';
    $param['hospital'] = $patient['hospital_name'];
    $param['status'] = 2;
    $param['dataPath'] = '';
    $param['reportPath'] = '';
    $param['result'] = '';
    $param['diagDoctor'] = '';
    $param['diagDoctorId'] = '00000000-0000-0000-0000-000000000000';
    $param['diagDate'] = '';
    $param['approveDoctor'] = '';
    $param['approveDoctorId'] = '00000000-0000-0000-0000-000000000000';
    $param['publishStatus'] = 1;
    $param['dataType'] = 5;
    $param['diagFlag'] = '';
    $param['objectName'] = $patient['url'];
    $param['appKey'] = '';
    $param['timestamp'] = 0;
    $param['appSign'] = '';
    try {
        $result = $client->__soapCall('SaveExamYZ', array('parameters' => $param));
        if ($result->SaveExamYZResult === true) {
            //echo 'ok';
            Logger::write('henan_agency.log', 'success : ' . $guardianId);
        } elseif ($result->SaveExamYZResult === false) {
            //echo 'ng';
            Logger::write('henan_agency.log', 'fail : ' . $guardianId);
            Logger::write('henan_agency_failed.log', $guardianId);
        } else {
            //echo 'other';
            Logger::write('henan_agency_other.log', $guardianId);
        }
        //var_dump($result);
    } catch (Exception $e) {
        Logger::write('henan_agency.log', $e->getMessage());
    }
} else {
    Logger::write('henan_agency.log', 'failed to new soap client.');
}
Logger::write('henan_agency.log', 'end: ' . $guardianId);

echo 'ok';
exit(0);
