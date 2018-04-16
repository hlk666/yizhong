<?php
$ret = array();
$ret['code'] = 0;
$hospital1 = ['hospital_id' => '119', 'hospital_name' => '羿中云平台'];
$hospital2 = ['hospital_id' => '132', 'hospital_name' => 'YZ-S'];
$hospital3 = ['hospital_id' => '141', 'hospital_name' => '羿中云平台2'];
$ret['hospitals'][] = $hospital1;
$ret['hospitals'][] = $hospital2;
$ret['hospitals'][] = $hospital3;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
