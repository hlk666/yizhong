<?php
$ret = array();
$ret['code'] = 0;
$hospital1 = ['hospital_id' => 1, 'hospital_name' => '羿中医疗1'];
$hospital2 = ['hospital_id' => 40, 'hospital_name' => '羿中医疗40'];
$ret['hospitals'][] = $hospital1;
$ret['hospitals'][] = $hospital2;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
