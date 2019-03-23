<?php
$file = PATH_DATA . 'shift.txt';
$txt = file_get_contents($file);

$ret['code'] = '0';
$ret['message'] = MESSAGE_SUCCESS;
$ret['shift_on'] = $txt;
api_exit($ret);
