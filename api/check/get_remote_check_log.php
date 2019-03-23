<?php
if (!isset($_GET['patients']) || empty($_GET['patients'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'patients']);
}
if (!isset($_GET['hour']) || empty($_GET['hour'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED .'hour']);
}

$ymd = date("Ymd");
$logFile = PATH_LOG . $ymd . $_GET['hour'] . 'all_params.log';
$list = explode(',', $_GET['patients']);
if (empty($list) || empty($list[0])) {
    api_exit(['code' => '1', 'message' => MESSAGE_PARAM]);
}

$result = array();
foreach ($list as $patientId) {
    $pattern = "/$ymd (\d{2}:\d{2}:\d{2})----array \(\s+  'entry' => '(client_remote_check|check_remote_check"
        . "|app_get_command|app_upload_data)',\s+'patient_id' => '$patientId'/U";
    preg_match_all($pattern, file_get_contents($logFile), $out);
    $data = '';
    if (!empty($out[0])) {
        $count = count($out[1]);
        for ($i = 0; $i < $count; $i++) {
            if ($out[2][$i] == 'check_remote_check' || $out[2][$i] == 'client_remote_check') {
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
    $result[] = [$patientId => $data];
}

api_exit(['list' => $result]);
