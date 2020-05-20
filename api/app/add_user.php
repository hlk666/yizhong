<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';
//require_once PATH_LIB . 'ShortMessageService.php';

validate_add_user($_POST);

$device = $_POST['device_id'];
$mode = $_POST['mode'];
$name = $_POST['name'];
$age = $_POST['age'];
$sex = $_POST['sex'];
$tel = $_POST['tel'];
$guardHospital = $_POST['guard_hospital'];

$tentativeDiagnose = isset($_POST['tentative_diagnose']) ? $_POST['tentative_diagnose'] : '';
$medicalHistory = isset($_POST['medical_history']) ? $_POST['medical_history'] : '';

//check_mode($mode, $guardHospital);

$hospitalInfo = Dbi::getDbi()->getHospitalByDevice($device);
if (VALUE_DB_ERROR === $hospitalInfo) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
if (empty($hospitalInfo)) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}
$registHospital = $hospitalInfo['hospital_id'];
if (empty($registHospital)) {
    api_exit(['code' => '1', '设备未绑定。']);
}

check_device($device, $registHospital);
/*
$ret = Dbi::getDbi()->changeOrderStatus($registHospital, $name);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}*/
$file = PATH_CONFIG . 'hospital_mode.txt';
if (file_exists($file)) {
    $config = explode(',', file_get_contents($file));
    if (in_array($registHospital, $config)) {
        $mode = 1;
    }
}

//$registHospital = $_POST['regist_hospital'];
//$guardHospital = $_POST['guard_hospital'];
$doctorId = 0;//will be used in future.
$doctorName = $_POST['doctor_name'];

$height = isset($_POST['height']) ? $_POST['height'] : '0';
$weight = isset($_POST['weight']) ? $_POST['weight'] : '0';
$bloodPressure = isset($_POST['blood_pressure']) ? $_POST['blood_pressure'] : '';
$sickRoom = isset($_POST['sickroom']) ? $_POST['sickroom'] : '';
$familyTel = isset($_POST['family_tel']) ? $_POST['family_tel'] : '0';
if ($registHospital == 203 || $registHospital == 486) {
    $hours = isset($_POST['guard_hours']) ? $_POST['guard_hours'] : 36;
} else {
    $hours = isset($_POST['guard_hours']) ? $_POST['guard_hours'] : 24;
}
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
    $polycardia = PARAM_POLYCARDIA;//$_POST['polycardia'];
    $bradycardia = PARAM_BRADYCARDIA;//$_POST['bradycardia'];
    $lead = PARAM_LEAD;//$_POST['lead'];
    $record_seconds = PARAM_MODE2_RECORD_TIME;//$_POST['record_seconds'];
    $regular_time = PARAM_REGULAR_TIME;//$_POST['regular_time'];
    $exminrate = PARAM_EXMINRATE;//$_POST['exminrate'];
    $stopbeat = PARAM_STOPBEAT;//$_POST['stopbeat'];
    $sthigh = PARAM_STHIGH;//$_POST['sthigh'];
    $stlow = PARAM_STLOW;//$_POST['stlow'];
    
    if ($registHospital == 480 || $registHospital == 199 || $registHospital == 40) {
        $twave = 'off';
    } else {
        $twave = PARAM_TWAVE;
    }
    if ($device > 80600000) {
        $twave = 'off';
    }
    //$twave = PARAM_TWAVE;//$_POST['twave'];
    
    $sEarlyBeat = 'on';//$_POST['s_early_beat'];
    $vEarlyBeat = 'on';//$_POST['v_early_beat'];
    $vDouble = 'on';//$_POST['v_double'];
    $vTwo = 'on';//$_POST['v_two'];
    $vThree = 'on';//$_POST['v_three'];
    $sDouble = 'on';//$_POST['s_double'];
    $sTwo = 'on';//$_POST['s_two'];
    $sThree = 'on';//$_POST['s_three'];
    $sSpeed = 'on';//$_POST['s_speed'];
    $vSpeed = 'on';//$_POST['v_speed'];
}

$guardianId = Dbi::getDbi()->flowGuardianAddUser($name, $sex, $age, $tel, $device, $registHospital, 
        $guardHospital, $mode, $hours, $lead, $doctorId, $sickRoom, $bloodPressure, $height, $weight,
        $familyTel, $tentativeDiagnose, $medicalHistory, $doctorName, $hospitalizationId);
if (VALUE_DB_ERROR === $guardianId) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}
//special action for zhongda start.
if (in_array($registHospital, [99999])) {
    Logger::write('zhongda_msg.log', 'regist hospital:' . $registHospital);
    $zhongda = Dbi::getDbi()->addZhongdaData($guardianId);
    if (VALUE_DB_ERROR === $guardianId) {
        Logger::write('zhongda_msg.log', 'failed to add zhongda_data.');
    }
}
//special action for zhongda end.
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
if ($registHospital == '1' || $registHospital == '40') {
    //not set cache.
} else {
    setRegistNotice($guardHospital, $mode);
    setRegistNotice('1', $mode);
}

updateWorkPool($guardianId);

if (VALUE_GT_ERROR === $ret) {
    api_exit(['code' => '3', 'message' => MESSAGE_GT_ERROR]);
}
/*
if ($registHospital != '1' && $registHospital != '40') {
    ShortMessageService::send('15684158646', '有新的注册信息，病人姓名：' . $name);
}
*/
api_exit_success();

function validate_add_user($post)
{
    if (!is_array($post) || empty($post)) {
        api_exit(['code' => '1', 'message' => '没有传递任何参数。']);
    }
    if (false === Validate::checkRequired($post['device_id'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
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
    if (false === Validate::checkRequired($post['guard_hospital'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'guard_hospital.']);
    }
    if (false === Validate::checkRequired($post['doctor_name'])) {
        api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'doctor_name.']);
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

function check_mode($mode, $hospital)
{
    if ($mode == 2) {
        return;
    }
    if (file_exists(PATH_CONFIG . 'mode.php')) {
        include_once PATH_CONFIG . 'mode.php';
        if ($mode == 1 && !in_array($hospital, $mode1)) {
            api_exit(['code' => '17', 'message' => '请选择异常模式(当前选择的是实时模式)。']);
        }
        if ($mode == 3 && !in_array($hospital, $mode3)) {
            api_exit(['code' => '17', 'message' => '请选择异常模式(当前选择的是单次模式)。']);
        }
    }
}

function updateWorkPool($guardianId)
{
    $file = '';
    $count = 9999;
    $path = PATH_DATA . 'guardian_on' . DIRECTORY_SEPARATOR;
    $fileList = scandir($path);
    foreach ($fileList as $f) {
        if ($f != '.' && $f != '..') {
            $guardianTxt = file_get_contents($path . $f);
            $guardianCount = count(explode(',', $guardianTxt));
            if ($count > $guardianCount) {
                $count = $guardianCount;
                $file = $path . $f;
            }
        }
    }
    file_put_contents($file, file_get_contents($file) . ',' . $guardianId);
}
