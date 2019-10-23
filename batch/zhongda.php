<?php
require 'common.php';

$ret = DbiAdmin::getDbi()->getDataForZhongda();
if (VALUE_DB_ERROR === $ret) {
    Logger::write('zhongda_msg.log', 'db error.');
    exit(-1);
}
if (empty($ret)) {
    Logger::write('zhongda_msg.log', 'no data');
    exit(0);
}

$pattern = '/一、(.*)二、诊断：\n\s*(.*)/s';
foreach ($ret as $row) {
    $file = PATH_ROOT . 'report' . DIRECTORY_SEPARATOR . $row['guardian_id'] . '.pdf';
    if (!file_exists($file)) {
        Logger::write('zhongda_msg.log', 'pdf not exists -' . $guardianId);
        continue;
    }
    $data = array();
    $data['guardian_id'] = $row['guardian_id'];
    $data['hospital_parent'] = $row['hospital_parent'];
    $data['doctor_idc'] = $row['doctor_idc'];
    $data['doctor_name'] = $row['doctor_name'];
    $data['hospital_child'] = $row['hospital_child'];
    $data['patient_name'] = $row['patient_name'];
    $data['patient_age'] = date('Y') - $row['birth_year'];
    $data['patient_sex'] = $row['patient_sex'];
    $data['report_time'] = $row['report_time'];
    /*
    $isRightFormat = preg_match($pattern, $diagnose, $matches);
    if ($isRightFormat === false || empty($matches)) {
        $data['treatment'] = '';
        $data['diagnose'] = $row['diagnose'];
    } else {
        $data['treatment'] = $matches[1];
        $data['diagnose'] = $matches[2];
    }
    */
    $data['pdf_binary'] = file_get_contents($file);
    
    $url = 'http://1312312312312312312';
    $ret = request($url, $data);
    //echo $ret;
    $retArray = json_decode($ret, true);
    if (isset($retArray['code']) && $retArray['code'] == '0') {
        $update = DbiAdmin::getDbi()->updateZhongdaData($guardianId);
        if (VALUE_DB_ERROR === $update) {
            Logger::write('zhongda_msg.log', 'db error.');
            exit(-1);
        }
        Logger::write('zhongda_msg.log', 'success-' . $guardianId);
    } else {
        Logger::write('zhongda_msg.log', $ret . '-' . $guardianId);
    }
}

//echo 'ok';
exit(0);

function request($url, $post)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

