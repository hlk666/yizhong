<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Logger.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}

$guardianId = $_GET['patient_id'];
//@todo add check mode here in future.

$file = PATH_CACHE_CMD . $guardianId . '.php';
if (file_exists($file)) {
    include $file;
    $result = array();
    $result['code'] = '0';
    $result['message'] = MESSAGE_SUCCESS;
    $result['polycardia'] = $info['mode2_polycardia'];
    $result['bradycardia'] = $info['mode2_bradycardia'];
    $result['record_seconds'] = $info['mode2_record_time'];
    $result['regular_time'] = $info['mode2_regular_time'];
    $result['premature_beat'] = $info['mode2_premature_beat'];
    $result['lead'] = $info['mode2_lead'];
    $result['combeatrhy'] = $info['mode2_combeatrhy'];
    $result['exminrate'] = $info['mode2_exminrate'];
    $result['stopbeat'] = $info['mode2_stopbeat'];
    $result['sthigh'] = $info['mode2_sthigh'];
    $result['stlow'] = $info['mode2_stlow'];
    $result['twave'] = $info['mode2_twave'];
    
    $result['s_early_beat'] = $info['s_early_beat'];
    $result['v_early_beat'] = $info['v_early_beat'];
    $result['v_double'] = $info['v_double'];
    $result['v_two'] = $info['v_two'];
    $result['v_three'] = $info['v_three'];
    $result['s_double'] = $info['s_double'];
    $result['s_two'] = $info['s_two'];
    $result['s_three'] = $info['s_three'];
    $result['s_speed'] = $info['s_speed'];
    $result['v_speed'] = $info['v_speed'];
    $result['polycardia_hour'] = $info['polycardia_hour'];
    $result['polycardia_times'] = $info['polycardia_times'];
    $result['bradycardia_hour'] = $info['bradycardia_hour'];
    $result['bradycardia_times'] = $info['bradycardia_times'];
    $result['sthigh_hour'] = $info['sthigh_hour'];
    $result['sthigh_times'] = $info['sthigh_times'];
    $result['stlow_hour'] = $info['stlow_hour'];
    $result['stlow_times'] = $info['stlow_times'];
    $result['s_early_beat_hour'] = $info['s_early_beat_hour'];
    $result['s_early_beat_times'] = $info['s_early_beat_times'];
    $result['v_early_beat_hour'] = $info['v_early_beat_hour'];
    $result['v_early_beat_times'] = $info['v_early_beat_times'];
    $result['stopbeat_hour'] = $info['stopbeat_hour'];
    $result['stopbeat_times'] = $info['stopbeat_times'];
    $result['v_double_hour'] = $info['v_double_hour'];
    $result['v_double_times'] = $info['v_double_times'];
    $result['v_two_hour'] = $info['v_two_hour'];
    $result['v_two_times'] = $info['v_two_times'];
    $result['v_three_hour'] = $info['v_three_hour'];
    $result['v_three_times'] = $info['v_three_times'];
    $result['s_double_hour'] = $info['s_double_hour'];
    $result['s_double_times'] = $info['s_double_times'];
    $result['s_two_hour'] = $info['s_two_hour'];
    $result['s_two_times'] = $info['s_two_times'];
    $result['s_three_hour'] = $info['s_three_hour'];
    $result['s_three_times'] = $info['s_three_times'];
    $result['s_speed_hour'] = $info['s_speed_hour'];
    $result['s_speed_times'] = $info['s_speed_times'];
    $result['v_speed_hour'] = $info['v_speed_hour'];
    $result['v_speed_times'] = $info['v_speed_times'];
    $result['exminrate_hour'] = $info['exminrate_hour'];
    $result['exminrate_times'] = $info['exminrate_times'];
    
    api_exit($result);
} else {
    Logger::writeCommonError('cache file not existed with ID:' . $guardianId);
    api_exit(['code' => '19', 'message' => '无法查看监护参数。']);
}
