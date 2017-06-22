<?php

if (!file_exists(PATH_CONFIG . 'diagnosis')) {
    api_exit(['code' => '99', 'message' => MESSAGE_OTHER_ERROR]);
}
$mstDiagnosis = file_get_contents(PATH_CONFIG . 'diagnosis');
$tempArray = array_filter(explode("\r\n", $mstDiagnosis));
$diagnosis = array();
foreach ($tempArray as $temp) {
    $row = explode(',', $temp);
    $diagnosis[] = ['id' => $row[0], 'text' => $row[1]];
}

$ret['code'] = '0';
$ret['message'] = MESSAGE_SUCCESS;
$ret['diagnosis'] = $diagnosis;

echo json_encode($ret, JSON_UNESCAPED_UNICODE);
exit;
