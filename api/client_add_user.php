<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';

validate_add_user($_POST);

$device = $_POST['device_id'];
check_device_used($device);

$mode = $_POST['mode'];
$name = $_POST['name'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$tel = $_POST['tel'];
$tentativeDiagnose = $_POST['tentative_diagnose'];
$medicalHistory = $_POST['medical_history'];
$registHospital = $_POST['regist_hospital'];
$guardHospital = $_POST['guard_hospital'];
$doctorId = 0;//will be used in future.
$doctorName = $_POST['doctor_name'];

$height = isset($_POST['height']) ? $_POST['height'] : '0';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '0';
$bloodPressure = isset($_POST['blood_pressure']) ? $_POST['blood_pressure'] : '';
$sickRoom = isset($_POST['sickroom']) ? $_POST['sickroom'] : '';
$familyTel = isset($_POST['family_tel']) ? $_POST['family_tel'] : '0';
$hours = isset($_POST['guard_hours']) ? $_POST['guard_hours'] : 0;

$polycardia = PARAM_POLYCARDIA;
$bradycardia = PARAM_BRADYCARDIA;
$lead = PARAM_LEAD;
if ('2' == $mode) {
    $polycardia = $_POST['polycardia'];
    $bradycardia = $_POST['bradycardia'];
    $lead = $_POST['lead'];
    $record_seconds = $_POST['record_seconds'];
    $regular_time = $_POST['regular_time'];
    $premature_beat = $_POST['premature_beat'];
    $combeatrhy = $_POST['combeatrhy'];
    $exminrate = $_POST['exminrate'];
    $stopbeat = $_POST['stopbeat'];
    $sthigh = $_POST['sthigh'];
    $stlow = $_POST['stlow'];
    $twave = $_POST['twave'];
}

$guardianId = Dbi::getDbi()->flowGuardianAddUser($name, $sex, $age, $tel, $device, $registHospital, 
        $guardHospital, $mode, $hours, $lead, $doctorId, $sickRoom, $bloodPressure, $height, $weight,
        $familyTel, $tentativeDiagnose, $medicalHistory, $doctorName);
if (VALUE_DB_ERROR === $guardianId) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
$invigilator = new Invigilator($guardianId, $hours);
$param = array();
if ('1' == $mode) {
    $param['mode1_polycardia'] = $polycardia;
    $param['mode1_bradycardia'] = $bradycardia;
    $param['mode1_lead'] = $lead;
}
if ('2' == $mode) {
    $param['mode2_record_time'] = $record_seconds;
    $param['mode2_polycardia'] = $polycardia;
    $param['mode2_bradycardia'] = $bradycardia;
    $param['mode2_lead'] = $lead;
    $param['mode2_regular_time'] = $regular_time;
    $param['mode2_premature_beat'] = $premature_beat;
    $param['mode2_exminrate'] = $exminrate;
    $param['mode2_combeatrhy'] = $combeatrhy;
    $param['mode2_stopbeat'] = $stopbeat;
    $param['mode2_sthigh'] = $sthigh;
    $param['mode2_stlow'] = $stlow;
    $param['mode2_twave'] = $twave;
}
if ('3' == $mode) {
    $param['mode3_polycardia'] = $polycardia;
    $param['mode3_bradycardia'] = $bradycardia;
    $param['mode3_lead'] = $lead;
}
$ret = $invigilator->create($param);
if (VALUE_PARAM_ERROR === $ret) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (VALUE_GT_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => '注册成功，但和设备通信失败。']);
}

$result = array();
$result['code'] = '0';
$result['message'] = '注册成功。';
api_exit($result);

function validate_add_user($post)
{
    if (!is_array($post) || empty($post)) {
        api_exit(['code' => '1', 'message' => '没有传递任何参数。']);
    }
    if (false === Validate::checkRequired($post['mode'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'mode.']);
    }
    if (false === Validate::checkRequired($post['name'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'name.']);
    }
    if (false === Validate::checkRequired($post['age'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'age.']);
    }
    if (false === Validate::checkRequired($post['sex'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'sex.']);
    }
    if (false === Validate::checkRequired($post['tel'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tel.']);
    }
    if (false === Validate::checkRequired($post['tentative_diagnose'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'tentative_diagnose.']);
    }
    if (false === Validate::checkRequired($post['medical_history'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'medical_history.']);
    }
    if (false === Validate::checkRequired($post['regist_hospital'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'regist_hospital.']);
    }
    if (false === Validate::checkRequired($post['guard_hospital'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'guard_hospital.']);
    }
    if (false === Validate::checkRequired($post['device_id'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
    }
    if (false === Validate::checkRequired($post['doctor_name'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_name.']);
    }
    
    if ('1' == $post['mode'] || '2' == $post['mode']) {
        if (false === Validate::checkRequired($post['guard_hours'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'guard_hours.']);
        }
    }
    
    if ('2' == $post['mode']) {
        if (false === Validate::checkRequired($post['polycardia'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'polycardia.']);
        }
        if (false === Validate::checkRequired($post['bradycardia'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'bradycardia.']);
        }
        if (false === Validate::checkRequired($post['lead'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'lead.']);
        }
        if (false === Validate::checkRequired($post['record_seconds'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'record_seconds.']);
        }
        if (false === Validate::checkRequired($post['regular_time'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'regular_time.']);
        }
        if (false === Validate::checkRequired($post['premature_beat'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'premature_beat.']);
        }
        if (false === Validate::checkRequired($post['combeatrhy'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'combeatrhy.']);
        }
        if (false === Validate::checkRequired($post['exminrate'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'exminrate.']);
        }
        if (false === Validate::checkRequired($post['stopbeat'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'stopbeat.']);
        }
        if (false === Validate::checkRequired($post['sthigh'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'sthigh.']);
        }
        if (false === Validate::checkRequired($post['stlow'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'stlow.']);
        }
        if (false === Validate::checkRequired($post['twave'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'twave.']);
        }
    }
}

function check_device_used($device)
{
    $guardian = Dbi::getDbi()->getGuardianByDevice($device);
    if (VALUE_DB_ERROR === $guardian) {
        api_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
    }
    if (!empty($guardian)) {
        $patient = Dbi::getDbi()->getPatient($guardian['patient_id']);
        if (VALUE_DB_ERROR === $patient) {
            api_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
        }
        if (empty($patient)) {
            $otherPatient = '其他用户(id:' . $guardian['patient_id'] . ')';
        } else {
            $otherPatient = $patient['patient_name'];
        }
        if ('0' == $guardian['status'] || '1' == $guardian['status']) {
            api_exit(['code' => '4', 'message' => $otherPatient . '正在使用该设备。']);
        }
    }
}
