<?php

$patientId = '5485';
$h = '08';
$ymd = date("Ymd");
$txtLog = file_get_contents($ymd . $h . 'all_params.log');
//var_dump($txtLog);

//$pattern = "/'entry' => '(.*)', 'patient_id' => '(.*)'/";
//$pattern = "/'entry' => '(.*)'/";
//$pattern = "/'entry' => 'client_read_ecg',\s+'ecg_id' => '303313'/";
$pattern = "/$ymd (\d{2}:\d{2}:\d{2})----array \(\s+  'entry' => '(client_remote_check|app_get_command|app_upload_data)',\s+'patient_id' => '$patientId'/U";
preg_match_all($pattern, $txtLog, $out);

if (empty($out[0])) {
    echo 'empty.';
} else {
    for ($i = 0; $i < count($out[1]); $i++) {
        echo $out[1][$i] . ' => ' . $out[2][$i] . '<br />';
    }
}