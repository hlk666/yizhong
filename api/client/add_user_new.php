<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';
require_once PATH_LIB . 'ShortMessageService.php';

validate_add_user($_POST);

$device = $_POST['device_id'];
$mode = $_POST['mode'];
$name = $_POST['name'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$tel = $_POST['tel'];
$tentativeDiagnose = isset($_POST['tentative_diagnose']) ? $_POST['tentative_diagnose'] : '';
$medicalHistory = isset($_POST['medical_history']) ? $_POST['medical_history'] : '';
$registHospital = $_POST['regist_hospital'];
$guardHospital = $_POST['guard_hospital'];
$doctorId = 0;//will be used in future.
$doctorName = $_POST['doctor_name'];

check_device($device, $registHospital);
if (!isset($_POST['ignore_repeat'])) {
    check_patient_repeat($registHospital, $name);
}

$height = isset($_POST['height']) ? $_POST['height'] : '0';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '0';
$bloodPressure = isset($_POST['blood_pressure']) ? $_POST['blood_pressure'] : '';
$sickRoom = isset($_POST['sickroom']) ? $_POST['sickroom'] : '';
$familyTel = isset($_POST['family_tel']) ? $_POST['family_tel'] : '0';
$hours = isset($_POST['guard_hours']) ? $_POST['guard_hours'] : 24;
$hospitalizationId = isset($_POST['hospitalization_id']) ? $_POST['hospitalization_id'] : '0';

$polycardiaHour = isset($_POST['polycardia_hour']) ? $_POST['polycardia_hour'] : '0';
$polycardiaTimes = isset($_POST['polycardia_times']) ? $_POST['polycardia_times'] : '0';
$bradycardiaHour = isset($_POST['bradycardia_hour']) ? $_POST['bradycardia_hour'] : '0';
$bradycardiaTimes = isset($_POST['bradycardia_times']) ? $_POST['bradycardia_times'] : '0';
$sthighHour = isset($_POST['sthigh_hour']) ? $_POST['sthigh_hour'] : '0';
$sthighTimes = isset($_POST['sthigh_times']) ? $_POST['sthigh_times'] : '0';
$stlowHour = isset($_POST['stlow_hour']) ? $_POST['stlow_hour'] : '0';
$stlowTimes = isset($_POST['stlow_times']) ? $_POST['stlow_times'] : '0';
$sEarlyBeatHour = isset($_POST['s_early_beat_hour']) ? $_POST['s_early_beat_hour'] : '0';
$sEarlyBeatTimes = isset($_POST['s_early_beat_times']) ? $_POST['s_early_beat_times'] : '0';
$vEarlyBeatHour = isset($_POST['v_early_beat_hour']) ? $_POST['v_early_beat_hour'] : '0';
$vEarlyBeatTimes = isset($_POST['v_early_beat_times']) ? $_POST['v_early_beat_times'] : '0';
$stopbeatHour = isset($_POST['stopbeat_hour']) ? $_POST['stopbeat_hour'] : '0';
$stopbeatTimes = isset($_POST['stopbeat_times']) ? $_POST['stopbeat_times'] : '0';
$vDoubleHour = isset($_POST['v_double_hour']) ? $_POST['v_double_hour'] : '0';
$vDoubleTimes = isset($_POST['v_double_times']) ? $_POST['v_double_times'] : '0';
$vTwoHour = isset($_POST['v_two_hour']) ? $_POST['v_two_hour'] : '0';
$vTwoTimes = isset($_POST['v_two_times']) ? $_POST['v_two_times'] : '0';
$vThreeHour = isset($_POST['v_three_hour']) ? $_POST['v_three_hour'] : '0';
$vThreeTimes = isset($_POST['v_three_times']) ? $_POST['v_three_times'] : '0';
$sDoubleHour = isset($_POST['s_double_hour']) ? $_POST['s_double_hour'] : '0';
$sDoubleTimes = isset($_POST['s_double_times']) ? $_POST['s_double_times'] : '0';
$sTwoHour = isset($_POST['s_two_hour']) ? $_POST['s_two_hour'] : '0';
$sTwoTimes = isset($_POST['s_two_times']) ? $_POST['s_two_times'] : '0';
$sThreeHour = isset($_POST['s_three_hour']) ? $_POST['s_three_hour'] : '0';
$sThreeTimes = isset($_POST['s_three_times']) ? $_POST['s_three_times'] : '0';
$sSpeedHour = isset($_POST['s_speed_hour']) ? $_POST['s_speed_hour'] : '0';
$sSpeedTimes = isset($_POST['s_speed_times']) ? $_POST['s_speed_times'] : '0';
$vSpeedHour = isset($_POST['v_speed_hour']) ? $_POST['v_speed_hour'] : '0';
$vSpeedTimes = isset($_POST['v_speed_times']) ? $_POST['v_speed_times'] : '0';
$exminrateHour = isset($_POST['exminrate_hour']) ? $_POST['exminrate_hour'] : '0';
$exminrateTimes = isset($_POST['exminrate_times']) ? $_POST['exminrate_times'] : '0';

$polycardia = PARAM_POLYCARDIA;
$bradycardia = PARAM_BRADYCARDIA;
$lead = PARAM_LEAD;
if ('2' == $mode) {
    $polycardia = $_POST['polycardia'];
    $bradycardia = $_POST['bradycardia'];
    $lead = $_POST['lead'];
    $record_seconds = $_POST['record_seconds'];
    $regular_time = $_POST['regular_time'];
    $exminrate = $_POST['exminrate'];
    $stopbeat = $_POST['stopbeat'];
    $sthigh = $_POST['sthigh'];
    $stlow = $_POST['stlow'];
    $twave = $_POST['twave'];
    
    $sEarlyBeat = $_POST['s_early_beat'];
    $vEarlyBeat = $_POST['v_early_beat'];
    $vDouble = $_POST['v_double'];
    $vTwo = $_POST['v_two'];
    $vThree = $_POST['v_three'];
    $sDouble = $_POST['s_double'];
    $sTwo = $_POST['s_two'];
    $sThree = $_POST['s_three'];
    $sSpeed = $_POST['s_speed'];
    $vSpeed = $_POST['v_speed'];
}

$startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;

$guardianId = Dbi::getDbi()->flowGuardianAddUser($name, $sex, $age, $tel, $device, $registHospital, 
        $guardHospital, $mode, $hours, $lead, $doctorId, $sickRoom, $bloodPressure, $height, $weight,
        $familyTel, $tentativeDiagnose, $medicalHistory, $doctorName, $hospitalizationId, $startTime);
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
    $param['mode2_exminrate'] = $exminrate;
    $param['mode2_stopbeat'] = $stopbeat;
    $param['mode2_sthigh'] = $sthigh;
    $param['mode2_stlow'] = $stlow;
    $param['mode2_twave'] = $twave;
    
    $param['s_early_beat'] = $sEarlyBeat;
    $param['v_early_beat'] = $vEarlyBeat;
    $param['v_double'] = $vDouble;
    $param['v_two'] = $vTwo;
    $param['v_three'] = $vThree;
    $param['s_double'] = $sDouble;
    $param['s_two'] = $sTwo;
    $param['s_three'] = $sThree;
    $param['s_speed'] = $sSpeed;
    $param['v_speed'] = $vSpeed;
    
    $param['polycardia_hour'] = $polycardiaHour;
    $param['polycardia_times'] = $polycardiaTimes;
    $param['bradycardia_hour'] = $bradycardiaHour;
    $param['bradycardia_times'] = $bradycardiaTimes;
    $param['sthigh_hour'] = $sthighHour;
    $param['sthigh_times'] = $sthighTimes;
    $param['stlow_hour'] = $stlowHour;
    $param['stlow_times'] = $stlowTimes;
    $param['s_early_beat_hour'] = $sEarlyBeatHour;
    $param['s_early_beat_times'] = $sEarlyBeatTimes;
    $param['v_early_beat_hour'] = $vEarlyBeatHour;
    $param['v_early_beat_times'] = $vEarlyBeatTimes;
    $param['stopbeat_hour'] = $stopbeatHour;
    $param['stopbeat_times'] = $stopbeatTimes;
    $param['v_double_hour'] = $vDoubleHour;
    $param['v_double_times'] = $vDoubleTimes;
    $param['v_two_hour'] = $vTwoHour;
    $param['v_two_times'] = $vTwoTimes;
    $param['v_three_hour'] = $vThreeHour;
    $param['v_three_times'] = $vThreeTimes;
    $param['s_double_hour'] = $sDoubleHour;
    $param['s_double_times'] = $sDoubleTimes;
    $param['s_two_hour'] = $sTwoHour;
    $param['s_two_times'] = $sTwoTimes;
    $param['s_three_hour'] = $sThreeHour;
    $param['s_three_times'] = $sThreeTimes;
    $param['s_speed_hour'] = $sSpeedHour;
    $param['s_speed_times'] = $sSpeedTimes;
    $param['v_speed_hour'] = $vSpeedHour;
    $param['v_speed_times'] = $vSpeedTimes;
    $param['exminrate_hour'] = $exminrateHour;
    $param['exminrate_times'] = $exminrateTimes;
}
if ('3' == $mode) {
    $param['mode3_polycardia'] = $polycardia;
    $param['mode3_bradycardia'] = $bradycardia;
    $param['mode3_lead'] = $lead;
}
$param['action'] = 'start';
$ret = $invigilator->create($param);
if (VALUE_PARAM_ERROR === $ret) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
//both of success and failing to send message to device
setRegistNotice($guardHospital, $mode);

if (VALUE_GT_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => MESSAGE_GT_ERROR]);
}

ShortMessageService::send('15684158646', '有新的注册信息，病人姓名：' . $name);

api_exit_success();

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
        if (false === Validate::checkRequired($post['s_early_beat'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 's_early_beat.']);
        }
        if (false === Validate::checkRequired($post['v_early_beat'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'v_early_beat.']);
        }
        if (false === Validate::checkRequired($post['v_double'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'v_double.']);
        }
        if (false === Validate::checkRequired($post['v_two'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'v_two.']);
        }
        if (false === Validate::checkRequired($post['v_three'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'v_three.']);
        }
        if (false === Validate::checkRequired($post['s_double'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 's_double.']);
        }
        if (false === Validate::checkRequired($post['s_two'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 's_two.']);
        }
        if (false === Validate::checkRequired($post['s_three'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 's_three.']);
        }
        if (false === Validate::checkRequired($post['s_speed'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 's_speed.']);
        }
        if (false === Validate::checkRequired($post['v_speed'])) {
            api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'v_speed.']);
        }
    }
}

function check_device($device, $hospital)
{
    $ret = Dbi::getDbi()->existedDeviceHospital($device, $hospital);
    if (false == $ret) {
        api_exit(['code' => '16', 'message' => '该设备不属于本医院。']);
    }
    $guardian = Dbi::getDbi()->getGuardianByDevice($device);
    if (VALUE_DB_ERROR === $guardian) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (!empty($guardian)) {
        $patient = Dbi::getDbi()->getPatient($guardian['patient_id']);
        if (VALUE_DB_ERROR === $patient) {
            api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
        }
        if (empty($patient)) {
            $otherPatient = '其他用户(id:' . $guardian['patient_id'] . ')';
        } else {
            $otherPatient = $patient['patient_name'];
        }
        if ('0' == $guardian['status'] || '1' == $guardian['status']) {
            api_exit(['code' => '17', 'message' => $otherPatient . '正在使用该设备。']);
        }
    }
}
function check_patient_repeat($hospital, $name)
{
    $ret = Dbi::getDbi()->getRepeatPatient($hospital, $name);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
    if (!empty($ret)) {
        api_exit(['code' => '18', 'message' => '重复注册。', 'data' => $ret]);
    }
}
