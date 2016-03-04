<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';


$title = '统计信息';
require 'header.php';

//@todo 昨天开了多少单子，医院别，其中实时单子多少个，异常单子多少个，单次单子多少个
//异常报警的条数多少，远程查房多少，定时报警多少，SOS多少
//@todo 迄今为止多少单子，医院别，其中实时单子多少个，异常单子多少个，单次单子多少个
//上面两个有"点击查看详细"的入口按钮
//@todo 昨天发生了多少次会诊，多少回复了，多少未回复
//@todo 有多少设备正在被使用，使用率多少
//@todo 上面的都要去掉测试数据


/*
$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : null;
$ret = Dbi::getDbi()->getStatistics();
if (VALUE_DB_ERROR === $ret) {
    echo '<script language="javascript">alert("服务器访问失败，请刷新重试。");</script>';
    $ret = array();
}
$count = count($ret);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$rows = 10;
$offset = ($page - 1) * $rows;
$lastPage = ceil($count / $rows);

if (1 === $page) {
    $ret = array_slice($ret, 0, $rows);
} else {
    $ret = Dbi::getDbi()->getDeviceList($hospital, $offset, $rows);
}

$htmlDevices = '';
foreach ($ret as $value) {
    $htmlDevices .= '<tr><td>' 
            . $value['hospital_name'] . '</td><td>'
            . $value['device_id'] . '</td><td>' 
            . '<a href="edit_device.php?action=edit&id=' . $value['device_id'] . '">点击修改</a></td><td>'  
            . '<a href="edit_device.php?action=del&id=' . $value['device_id'] . '">点击删除</a></td></tr>';
}
$paging = getPaging($page, $lastPage);
echo <<<EOF
  <table class="table table-striped">
    <thead>
      <tr>
        <th>医院名</th>
        <th>设备ID</th>
        <th>修改信息</th>
        <th>删除信息</th>
      </tr>
    </thead>
    <tbody>$htmlDevices</tbody>
  </table>
<div style="text-align:right;">
<ul class="pagination">$paging</ul>
<div>
EOF;
require 'tpl/footer.tpl';
