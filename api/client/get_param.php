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
    api_exit($result);
} else {
    Logger::writeCommonError('cache file not existed with ID:' . $guardianId);
    api_exit(['code' => '19', 'message' => '参数配置信息不存在，请联系管理员。']);
}
