<?php
require PATH_LIB . 'QinFangKangJian.php';

//$guardianId = $_GET['id'];
//$yizongDoctorId = '882';

//$obj = new QinFangKangJian();

$time = $_GET['t'];
$key = $_GET['k'];

if (!empty($key)) {
    $obj = new QinFangKangJian();
    $ret = $obj->checkBaseParam($time, $key);
    echo $ret;
} else {
    list($usec, $sec) = explode(' ', microtime());
    $time1 = ($sec . substr($usec, 2, 3));
    echo $time1;
    echo '<br>';
    
    echo md5('hebeishengeryuan2021' . $time1);
}

/*
$ret = $obj->regist($guardianId);
if ($ret === false) {
    echo 'error, please check log info.';
} else {
    echo 'success';
}
*/
/*
$ret = $obj->upload($guardianId);
if ($ret === false) {
    echo 'error, please check log info.';
} else {
    echo 'success';
}
*/
/*
$ret = $obj->report($guardianId, $yizongDoctorId);
if ($ret === false) {
    echo 'error, please check log info.';
} else {
    echo 'success';
}
*/
