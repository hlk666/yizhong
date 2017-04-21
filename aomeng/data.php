<?php
exit;
require_once 'Dbi.php';

$hospitalId = 19;
$hospitalName = '淄川区黑旺卫生院';
$doctors = ['殷美霞'];
$dataCount = 158;
$sTime = strtotime('2015-04-23');

$eTime = strtotime('2017-04-17');
$doctorCount = count($doctors);
$names = file_get_contents('names.txt');
$arrNames = explode(',', $names);

for ($i = 1; $i <= $dataCount; $i++) {
    $randPatient = rand(0, 20000);
    $patientName = $arrNames[$randPatient];
    
    $startTime = date('Y-m-d H:i:s', mt_rand($sTime, $eTime));
    $endTime = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($startTime)));
    
    $deviceId = 80200000 + $hospitalId * 100 + rand(1, 10);
    
    $randDoctor = rand(0, $doctorCount - 1);
    $doctorName = $doctors[$randDoctor];
    
    Dbi::getDbi()->addData($hospitalId, $hospitalName, $patientName, $startTime, $endTime, $deviceId, $doctorName);
}
echo date('Y-m-d H:i:s');
