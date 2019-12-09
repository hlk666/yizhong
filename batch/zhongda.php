<?php
require 'common.php';

$baseUrl = 'http://101.200.174.235/test.php?id=';
$token = 'yizhong123456789';

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
                $data = array('token' => $token);
                $data['hospital_id'] = $row['hospital_id'];
                $data['hospital_name'] = $row['hospital_name'];
                $data['contact'] = $row['contact'];
                $data['hospital_tel'] = $row['hospital_tel'];
                
                $url = $baseUrl . 'hospital';
                $ret = request($url, $data);
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
                $data = array('token' => $token);
                $data['doctor_id'] = $row['doctor_id'];
                $data['doctor_name'] = $row['doctor_name'];
                $data['doctor_tel'] = $row['doctor_tel'];
                $data['doctor_idc'] = $row['doctor_idc'];

                $url = $baseUrl . 'doctor';
                $ret = request($url, $data);
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
    $data = array('token' => $token);
    $data['guardian_id'] = $row['guardian_id'];
    $data['hospital_id'] = $row['hospital_id'];
    $data['patient_name'] = $row['patient_name'];
    $data['birth_year'] = $row['birth_year'];
    $data['sex'] = $row['sex'];
    $data['patient_tel'] = $row['patient_tel'];
    $data['patient_idc'] = $row['patient_idc'];
    $data['start_time'] = $row['start_time'];
    
    $url = $baseUrl . 'regist';
    $ret = request($url, $data);
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
    $data = array('token' => $token);
    $data['guardian_id'] = $row['guardian_id'];
    $data['report_hospital_id'] = $row['report_hospital_id'];
    $data['doctor_id'] = $row['doctor_id'];
    $data['report_time'] = $row['report_time'];
    $data['pdf_binary'] = file_get_contents($file);
    $diagnosis = $row['diagnosis'];
    
    $isRightFormat = preg_match($pattern, $diagnosis, $matches);
    if ($isRightFormat === false || empty($matches)) {
        $data['diagnosis'] = $diagnosis;
    } else {
        $data['diagnosis'] = $matches[2];
    }
    $url = $baseUrl . 'report';
    $ret = request($url, $data);
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
