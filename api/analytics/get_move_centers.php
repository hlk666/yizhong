<?php
$ret = array();
$ret['code'] = 0;
$hospital1 = ['hospital_id' => '119', 'hospital_name' => '羿中云平台'];
$hospital2 = ['hospital_id' => '132', 'hospital_name' => '羿中云平台3'];
$hospital3 = ['hospital_id' => '139', 'hospital_name' => '羿中云平台-张元泽'];
$hospital4 = ['hospital_id' => '140', 'hospital_name' => '羿中云平台4'];
$hospital5 = ['hospital_id' => '141', 'hospital_name' => '羿中云平台2'];
$hospital6 = ['hospital_id' => '743', 'hospital_name' => '羿中云平台5'];
$ret['hospitals'][] = $hospital1;
$ret['hospitals'][] = $hospital2;
$ret['hospitals'][] = $hospital3;
$ret['hospitals'][] = $hospital4;
$ret['hospitals'][] = $hospital5;
$ret['hospitals'][] = $hospital6;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
