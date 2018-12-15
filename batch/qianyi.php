<?php
require 'common.php';

$ret = DbiAdmin::getDbi()->getDataForQianyi();
if (VALUE_DB_ERROR === $ret) {
    Logger::write('qianyi_msg.log', 'db error.');
    exit(-1);
}
if (empty($ret)) {
    Logger::write('qianyi_msg.log', 'no data');
    exit(0);
}

$cityList = [
                1000 => '370100',
                1007 => '370200',
                1016 => '370300',
                1022 => '370400',
                1025 => '370500',
                1042 => '370600',
                1032 => '370700',
                2900 => '370800',
                1112 => '370900',
                1053 => '371000',
                1108 => '371100',
                1058 => '371200',
                1072 => '371300',
                1060 => '371400',
                1060 => '371500',
                1060 => '371600',
                1099 => '371700'
];

foreach ($ret as $row) {
    $guardianId = $row['guardian_id'];
    $city = $cityList[$row['city']];
    $level = $row['level'];
    $startTime = $row['start_time'];
    $patientName = $row['patient_name'];
    $patientAge = date('Y') - $row['birth_year'];
    $patientSex = $row['sex'];
    $reportTime = $row['report_time'];
    $diagnose = $row['diagnose'];
    $hospitalName = $row['hospital_name'];
    $doctorName = $row['regist_doctor_name'];
    
    $data = array();
    $data['prim_no'] = $guardianId;
    $data['requserid'] = '6452ca61f615454b87fe46f7ae0a4a33';
    $data['reqhospitalid'] = 'e818e077b769428fa7802b3a385f1915';
    $data['reqhospital'] = '千佛山心电平台';
    $data['reqdepartmentid'] = '233602b4764f483b88ae12824557057b';
    $data['reqdepartment'] = '其他备用科室';
    $data['self_hosp'] = $hospitalName;
    $data['self_doc'] = $doctorName;
    $data['province'] = '370000';
    $data['city'] = $city;
    $data['hos_level'] = $level;
    $data['apply_time'] = $startTime;
    $data['pati_name'] = $patientName;
    $data['pati_identity'] = 'B';
    $data['pati_age'] = $patientAge;
    $data['pati_gender'] = $patientSex;
    $data['prim_status'] = '50';
    $data['cons_type'] = 'ecg';
    $data['qiantime'] = $startTime;
    $data['fenzhen_time'] = $startTime;
    $data['finish_time'] = $reportTime;
    $data['tria_hosp_id'] = '27015ad794364307850dbd162789f77b';
    $data['tria_hosp_name'] = '山东省千佛山医院';
    $data['tria_doct_id'] = '8559afbd616347fe8470ac373389686b';
    $data['tria_doct_name'] = '胡和生';
    $data['tria_dep_id'] = '191fdc15219c4eea9f6546b772c88a4b';
    $data['tria_dep_name'] = '心血管内科';
    $data['tria_sub_dept_id'] = 'ef7b6ff59ccc43c3affcf5527b980567';
    $data['tria_sub_dept_name'] = '保健心血管内二科';
    
    
    $data['cons_mode'] = '1';
    $data['diagnose'] = $diagnose;
    //$data['attechmentlist'][] = ['filename' => "$guardianId.pdf", 'attachmenttype' => '4', 'url' => "http://101.200.174.235/report/$guardianId.pdf"];
    
    $url = 'http://113.128.194.226:38901/TMSServer/api/v2/server.do';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    $param = '{"LOGICNAME":"saveCompleteConsultation","TOKEN":"","MESSAGEID":"202999","DATAS":' . $json . '}';
    //echo $param;
    $ret = request($url, $param);
    //echo $ret;
    $retArray = json_decode($ret, true);
    if (isset($retArray['code']) && $retArray['code'] == '00') {
        $update = DbiAdmin::getDbi()->updateQianyiData($guardianId);
        if (VALUE_DB_ERROR === $update) {
            Logger::write('qianyi_msg.log', 'db error.');
            exit(-1);
        }
        Logger::write('qianyi_msg.log', 'success-' . $guardianId);
    } else {
        Logger::write('qianyi_msg.log', $ret . '-' . $guardianId);
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

