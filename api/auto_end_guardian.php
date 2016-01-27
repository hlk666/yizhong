<?php
require 'E:\wamp\www\config\config.php';
require PATH_LIB . 'Invigilator.php';

$files = scandir(PATH_CACHE_CMD);
foreach ($files as $file) {
    $tmp = explode('.', $file);
    $id = '0';
    if ($tmp[1] == 'php') {
        $id = $tmp[0];
    }
    check_guardian($id);
}
echo 'success.';

function check_guardian($id)
{
    $file = PATH_CACHE_CMD . $id . '.php';
    //impossible case
    if (!file_exists($file)) {
        return;
    }
    
    include $file;
    //if later than end_time, send command of 'end' and not backup this file.
    if ($info['end_time'] != '' && time() >= $info['end_time']) {
        $invigilator = new Invigilator($id);
        $ret = $invigilator->create(['action' => 'end']);
        return;
    }
    
    //if exists command not token by device, not backup this file.
    if (!empty($command)) {
        return;
    }
    
    if ($info['end_time'] == '') {
        $bkFile = PATH_CACHE_CMD_BK . $id . '_' . date('YmdHis') . '.php';
        rename($file, $bkFile);
    }
}
