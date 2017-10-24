<?php
if (!isset($_GET['patient_id']) || empty($_GET['patient_id'])) {
    analytics_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'patient_id']);
}
if (!isset($_GET['hour']) || empty($_GET['hour'])) {
    analytics_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'hour']);
}
$patientId = $_GET['patient_id'];
$hour = $_GET['hour'];

$ymd = date("Ymd");
$logFile = PATH_LOG . $ymd . $hour . 'all_params.log';
$pattern = "/$ymd (\d{2}:\d{2}:\d{2})----array \(\s+  'entry' => '(client_remote_check" 
        . "|app_get_command|app_upload_data)',\s+'patient_id' => '$patientId'/U";
preg_match_all($pattern, file_get_contents($logFile), $out);
$data = '';
if (!empty($out[0])) {
    $count = count($out[1]);
    for ($i = 0; $i < $count; $i++) {
        if ($out[2][$i] == 'client_remote_check') {
            $action = '发出远程查房命令';
        } elseif ($out[2][$i] == 'app_get_command') {
            $action = 'App响应';
        } elseif ($out[2][$i] == 'app_upload_data') {
            $action = '上传数据';
        } else {
            $action = '其他';
        }
        $data .= $out[1][$i] . ',' . $action . ';';
    }
}

$ret = array();
$ret['code'] = 0;
$ret['data'] = $data;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
