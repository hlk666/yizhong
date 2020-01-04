<?php
require 'common.php';

$baseUrl = 'http://172.81.254.132:9099/hospital/';
//$baseUrl = 'http://101.200.174.235/test.php?id=';
$token = 'dndxfszdyy';

//hospital data start
$hospitalFile = PATH_DATA . 'zhongda' . DIRECTORY_SEPARATOR . 'hospital.txt';
if (!file_exists($hospitalFile)) {
    Logger::write('zhongda_msg.log', 'hospital file not exists.');
} else {
    $hospitals = file_get_contents($hospitalFile);
    file_put_contents($hospitalFile, '');
    if (empty($hospitals)) {
        Logger::write('zhongda_msg.log', 'no hospital data.');
    } else {
        $hospitalList = DbiAdmin::getDbi()->getDataForZhongdaHospital($hospitals);
        if (VALUE_DB_ERROR === $hospitalList) {
            Logger::write('zhongda_msg.log', 'db error.');
            exit(-1);
        }
        if (empty($hospitalList)) {
            Logger::write('zhongda_msg.log', 'can not get hospital data from db.');
        } else {
            foreach ($hospitalList as $row) {
                $data = array();
                $data['hosopitalId'] = $row['hospital_id'];
                $data['hospitalName'] = $row['hospital_name'];
                $data['contact'] = $row['contact'];
                $data['hospitalTel'] = $row['hospital_tel'];
                
                $url = $baseUrl . 'addHospitalInfo';
                $ret = request($url, create_data($token, $data));
                $retArray = json_decode($ret, true);
                if (isset($retArray['code']) && $retArray['code'] == '0') {
                    Logger::write('zhongda_msg.log', 'hospital-success-' . $data['hospital_id']);
                } else {
                    Logger::write('zhongda_msg.log', $ret . '-' . $data['hospital_id']);
                }
            }
        }
    }
}
//hospital data end

//doctor data start
$doctorFile = PATH_DATA . 'zhongda' . DIRECTORY_SEPARATOR . 'doctor.txt';
if (!file_exists($doctorFile)) {
    Logger::write('zhongda_msg.log', 'doctor file not exists.');
} else {
    $doctors = file_get_contents($doctorFile);
    file_put_contents($doctorFile, '');
    if (empty($doctors)) {
        Logger::write('zhongda_msg.log', 'no doctor data.');
    } else {
        $doctorList = DbiAdmin::getDbi()->getDataForZhongdaDoctor($doctors);
        if (VALUE_DB_ERROR === $doctorList) {
            Logger::write('zhongda_msg.log', 'db error.');
            exit(-1);
        }
        if (empty($doctorList)) {
            Logger::write('zhongda_msg.log', 'can not get doctor data from db.');
        } else {
            foreach ($doctorList as $row) {
                $data = array();
                $data['doctorId'] = $row['doctor_id'];
                $data['doctorName'] = $row['doctor_name'];
                $data['doctorTel'] = $row['doctor_tel'];
                $data['doctorIdc'] = $row['doctor_idc'];

                $url = $baseUrl . 'addDoctorInfo';
                $ret = request($url, create_data($token, $data));
                $retArray = json_decode($ret, true);
                if (isset($retArray['code']) && $retArray['code'] == '0') {
                    Logger::write('zhongda_msg.log', 'doctor-success-' . $data['doctor_id']);
                } else {
                    Logger::write('zhongda_msg.log', $ret . '-' . $data['doctor_id']);
                }
            }
        }
    }
}
//doctor data end

//regist data start
$ret = DbiAdmin::getDbi()->getDataForZhongdaRegist();
if (VALUE_DB_ERROR === $ret) {
    Logger::write('zhongda_msg.log', 'db error.');
    exit(-1);
}
if (empty($ret)) {
    Logger::write('zhongda_msg.log', 'no regist data');
}

foreach ($ret as $row) {
    $data = array();
    $data['guardianId'] = $row['guardian_id'];
    $data['hospitalId'] = $row['hospital_id'];
    $data['patientName'] = $row['patient_name'];
    $data['birthYear'] = $row['birth_year'];
    $data['sex'] = $row['sex'];
    $data['patientTel'] = $row['patient_tel'];
    $data['patientIdc'] = $row['patient_idc'];
    $data['startTime'] = $row['start_time'];
    
    $url = $baseUrl . 'addPatientInfo';
    $ret = request($url, create_data($token, $data));
    $retArray = json_decode($ret, true);
    if (isset($retArray['code']) && $retArray['code'] == '0') {
        $update = DbiAdmin::getDbi()->updateZhongdaData($row['guardian_id'], '1');
        if (VALUE_DB_ERROR === $update) {
            Logger::write('zhongda_msg.log', 'db error.');
            exit(-1);
        }
        Logger::write('zhongda_msg.log', 'regist success-' . $row['guardian_id']);
    } else {
        Logger::write('zhongda_msg.log', $ret . '-' . $row['guardian_id']);
    }
}

//传完注册数据要更新成1。
//0：注册、1：推送完毕、2：报告、3：推送完毕
//regist data end

//report data start
$ret = DbiAdmin::getDbi()->getDataForZhongdaReport();
if (VALUE_DB_ERROR === $ret) {
    Logger::write('zhongda_msg.log', 'db error.');
    exit(-1);
}
if (empty($ret)) {
    Logger::write('zhongda_msg.log', 'no report data');
}

$pattern = '/一、(.*)二、诊断：\n\s*(.*)/s';
foreach ($ret as $row) {
    $file = PATH_ROOT . 'report' . DIRECTORY_SEPARATOR . $row['guardian_id'] . '.pdf';
    if (!file_exists($file)) {
        Logger::write('zhongda_msg.log', 'pdf not exists -' . $row['guardian_id']);
        continue;
    }
    $data = array();
    $data['guardianId'] = $row['guardian_id'];
    $data['reportHospitalId'] = $row['report_hospital_id'];
    $data['doctorId'] = $row['doctor_id'];
    $data['reportTime'] = $row['report_time'];
    $data['pdfUrl'] = 'http://101.200.174.235/zhongda_report/' . $row['guardian_id'] . '.pdf';
    $diagnosis = $row['diagnosis'];
    
    $isRightFormat = preg_match($pattern, $diagnosis, $matches);
    if ($isRightFormat === false || empty($matches)) {
        $data['diagnosisi'] = $diagnosis;
    } else {
        $data['diagnosisi'] = $matches[2];
    }
    $data['diagnosisi'] = str_replace('%', '%25', $data['diagnosisi']);
    $url = $baseUrl . 'addReportInfo';
    $ret = request($url, create_data($token, $data));
    //$data1 = array('token' => $token, 'entryList' => [$data]);
    //file_put_contents(date('YmdHis') . '.log', json_encode($data1));
    //$ret = request1($url, $data1);
    $retArray = json_decode($ret, true);
    if (isset($retArray['code']) && $retArray['code'] == '0') {
        $update = DbiAdmin::getDbi()->updateZhongdaData($row['guardian_id'], '3');
        if (VALUE_DB_ERROR === $update) {
            Logger::write('zhongda_msg.log', 'db error.');
            exit(-1);
        }
        Logger::write('zhongda_msg.log', 'report success-' . $row['guardian_id']);
    } else {
        Logger::write('zhongda_msg.log', $ret . '-' . $row['guardian_id']);
    }
}
//report data end

//echo 'ok';
exit(0);

function create_data($token, $data)
{
    $ret = '{"tocken":"' . $token . '","entryList":[{';
    foreach ($data as $key => $value) {
        $ret .= '"' . $key . '":"' . $value . '",';
    }
    $ret = substr($ret, 0, -1);
    $ret .= '}]}';
    //file_put_contents(date('YmdHis') . '.log', $ret);
    return $ret;
}
function request1($url, $post)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Content-Length:' . strlen(json_encode($post))));
    
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
