<?php

require '../config/path.php';
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'RemoteCommand.php';
/*
if (!RemoteCommand::validate($_GET)) {
    echo json_encode(RemoteCommand::getError());
    exit;
}

$ret = RemoteCommand::setCommand($_GET['id']);
if (VALUE_COMMON_ERROR == $ret) {
    echo json_encode(RemoteCommand::getError());
    exit;
}

echo 'success.';
*/


function validate()
{
    if (!isset($_GET['id'])) {
        return false;
    }
    
    $id = trim($_GET['id']);
    if (empty($id)) {
        return false;
    }
    
    //@todo not use POST, only use GET
    return $_GET;
}
$data = validate();
if (false === $data) {
    echo 'param error.';
    exit;
}

if (isset($data['status'])) {
    unset($data['status']);
}
/*************** set default command start. ***************/
$mode = '0';
$status = isset($data['mode']) ? ($data['mode'] == 3 ? '3' : '0') : '0';
$card = 'master';
$all_time = 24;
$check_info = 'off';

$mode1_polycardia = 120;
$mode1_bradycardia = 50;
$mode1_lead = 'V5';
$mode2_record_time = 20;
$mode2_polycardia = 120;
$mode2_bradycardia = 50;
$mode2_regular_time = 1;
$mode2_premature_beat = 8;
$mode2_arrhythmia = 'on';
$mode2_pacemaker = 'on';
$mode2_lead = 'V5';
$mode3_polycardia = 120;
$mode3_bradycardia = 50;
$mode3_lead = 'V5';
$mode3_record_time = 20;

$start_time = '';
$end_time = '';
/*************** set default command end. ***************/

$file = PATH_CACHE_CMD . $data['id'] . '.php';
if (file_exists($file)) {
    include $file;
    
    if ($command['status'] == 2) {
        unlink($file);
    } else {
        $mode = $command['mode'];
        $status = $command['status'];
        $card = $command['card'];
        $all_time = $command['all_time'];
        $check_info = $command['check_info'];
        $mode1_polycardia = $command['mode1_polycardia'];
        $mode1_bradycardia = $command['mode1_bradycardia'];
        $mode1_lead = $command['mode1_lead'];
        $mode2_record_time = $command['mode2_record_time'];
        $mode2_polycardia = $command['mode2_polycardia'];
        $mode2_bradycardia = $command['mode2_bradycardia'];
        $mode2_regular_time = $command['mode2_regular_time'];
        $mode2_premature_beat = $command['mode2_premature_beat'];
        $mode2_arrhythmia = $command['mode2_arrhythmia'];
        $mode2_pacemaker = $command['mode2_pacemaker'];
        $mode2_lead = $command['mode2_lead'];
        $mode3_polycardia = $command['mode3_polycardia'];
        $mode3_bradycardia = $command['mode3_bradycardia'];
        $mode3_lead = $command['mode3_lead'];
        $mode3_record_time = $command['mode3_record_time'];
        $start_time = $command['start_time'];
        $end_time = $command['end_time'];
    }
    
}

$keysCommand = array(
        'mode', 'action', 'card', 'all_time', 'check_info', 
        'mode1_polycardia', 'mode1_bradycardia', 'mode1_lead', 
        'mode2_record_time', 'mode2_polycardia', 'mode2_bradycardia', 
        'mode2_regular_time', 'mode2_premature_beat', 'mode2_arrhythmia', 
        'mode2_pacemaker', 'mode2_lead', 
        'mode3_polycardia', 'mode3_bradycardia', 'mode3_lead', 'mode3_record_time');
$keysParam = array_keys($data);
$cmdNew = array_intersect($keysCommand, $keysParam);
extract($data);

if ($mode == 1 || $mode == 2) {
    if (isset($data['action'])) {
        if ($data['action'] == 'start' && $end_time == '') {
            $status = 1;
            $start_time = time();
            $end_time = $start_time + $all_time * 3600;
        }
        if ($data['action'] == 'end') {
            $status = 2;
            $start_time = '';
            $end_time = '';
        }
    }
}

$template = "<?php
\$command = array();
\$command['mode'] = '$mode';
\$command['status'] = '$status';
\$command['card'] = '$card';
\$command['all_time'] = '$all_time';
\$command['check_info'] = '$check_info';
\$command['mode1_polycardia'] = '$mode1_polycardia';
\$command['mode1_bradycardia'] = '$mode1_bradycardia';
\$command['mode1_lead'] = '$mode1_lead';
\$command['mode2_record_time'] = '$mode2_record_time';
\$command['mode2_polycardia'] = '$mode2_polycardia';
\$command['mode2_bradycardia'] = '$mode2_bradycardia';
\$command['mode2_regular_time'] = '$mode2_regular_time';
\$command['mode2_premature_beat'] = '$mode2_premature_beat';
\$command['mode2_arrhythmia'] = '$mode2_arrhythmia';
\$command['mode2_pacemaker'] = '$mode2_pacemaker';
\$command['mode2_lead'] = '$mode2_lead';
\$command['mode3_polycardia'] = '$mode3_polycardia';
\$command['mode3_bradycardia'] = '$mode3_bradycardia';
\$command['mode3_lead'] = '$mode3_lead';
\$command['mode3_record_time'] = '$mode3_record_time';
\$command['start_time'] = '$start_time';
\$command['end_time'] = '$end_time';\n\n";

$template .= "\$commandNew = array();\n";
foreach ($cmdNew as $key => $value) {
    $template .= '$commandNew[\'' . $value . '\'] = \'' . $data[$value] . "';\n";
}

$handle = fopen($file, 'w');
fwrite($handle, $template);
fclose($handle);
