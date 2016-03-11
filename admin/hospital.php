<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院列表';
require 'header.php';

$ret = DbiAdmin::getDbi()->getHospitalList();
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
    $ret = DbiAdmin::getDbi()->getHospitalList($offset, $rows);
}

$htmlHospitals = '';
foreach ($ret as $value) {
    $htmlHospitals .= '<tr><td>' 
            . $value['hospital_id'] . '</td><td>'
            . $value['hospital_name'] . '</td><td>'
            . $value['login_name'] . '</td><td>'
            . '<button type="button" class="btn btn-xs btn-warning" onclick="javascript:editHospital(' 
                . $value['hospital_id'] . ')">修改</button></td><td>'
            . '<button type="button" class="btn btn-xs btn-info" onclick="javascript:editRelation(' 
                . $value['hospital_id'] . ')">配置</button></td><td>'
            . '<button type="button" class="btn btn-xs btn-danger" onclick="javascript:deleteHospital(' 
                . $value['hospital_id'] . ')">删除</button></td></tr>';
}
$paging = getPaging($page, $lastPage);

echo <<<EOF
<table class="table table-striped">
<thead>
  <tr>
    <th>ID</th>
    <th>医院名</th>
    <th>管理员用户</th>
    <th>基本信息</th>
    <th>上级医院</th>
    <th>删除医院</th>
  </tr>
</thead>
<tbody>$htmlHospitals</tbody>
</table>
<div style="text-align:right;">
<ul class="pagination">$paging</ul>
<div>
EOF;
require 'tpl/footer.tpl';
