<?php
$ret = array();
$ret['code'] = 0;
$hospital = ['hospital_id' => '119', 'hospital_name' => '羿中医疗分析中心'];
$ret['hospitals'][] = $hospital;

analytics_exit($ret);

function analytics_exit(array $ret)
{
    echo json_encode($ret, JSON_UNESCAPED_UNICODE);
    exit;
}
