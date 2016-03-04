<?php
require '../common.php';
$title = '医院-设备列表';
require 'header.php';

$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : null;
$ret = Dbi::getDbi()->getDeviceList($hospital);
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
