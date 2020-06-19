<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '沟通管理';
require 'header.php';

$agency = isset($_GET['agency']) ? $_GET['agency'] : '';
$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$commuTitle = isset($_GET['title']) ? $_GET['title'] : '';

$ret = DbiAdmin::getDbi()->getAgencyList();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$htmlAgency = '<option value="0">请选择代理商</option>';
foreach ($ret as $value) {
    if ($agency == $value['agency_id']) {
        $htmlAgency .= '<option value="' . $value['agency_id'] . '" selected>' . $value['name'] . '</option>';
    } else {
        $htmlAgency .= '<option value="' . $value['agency_id'] . '">' . $value['name'] . '</option>';
    }
}

$ret = DbiAdmin::getDbi()->getAgencyList();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
/*
$htmlTitle = '<option value="0">请选择主题</option>';
foreach ($ret as $value) {
    if ($titleText == $value['title']) {
        $htmlTitle .= '<option value="' . $value['title_id'] . '" selected>' . $value['title'] . '</option>';
    } else {
        $htmlTitle .= '<option value="' . $value['title_id'] . '">' . $value['title'] . '</option>';
    }
}
*/

$ret = DbiAdmin::getDbi()->getCommuTitle($agency, $hospital, $commuTitle, $status);
if (VALUE_DB_ERROR === $ret) {
    echo '<script language="javascript">alert("服务器访问失败，请刷新重试。");</script>';
    $ret = array();
}

$htmlCommu = '';
foreach ($ret as $value) {
    if ($value['status'] < 2) {
        $isAdd = '<a href="commu_content.php?title_id=' . $value['title_id'] . '">录入沟通</a>';
    } else {
        $isAdd = '<a href="commu_content.php?title_id=' . $value['title_id'] . '">查看历史</a>';
    }
    $htmlCommu .= '<tr><td>' 
            . $value['agency_name'] . '</td><td>'
            . $value['hospital_name'] . '</td><td>'
            . $value['title'] . '</td><td>'
            . $value['create_time'] . '</td><td>'
            . $value['next_time'] . '</td><td>'
            . ($value['status'] == 2 ? '已结束' : '进行中') . '</td><td>'
            . '<a href="commu_content.php?title_id=' . $value['title_id'] . '">点击</a></td></tr>';
}

echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">代理商</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="agency">$htmlAgency</select>
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">医院ID</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="hospital" value="$hospital">
  </div>
  <!--<div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">主题</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="title">$htmlTitle</select>
  </div>-->
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">状态</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="status">
        <option value="0">全部</option>
        <option value="1" selected>进行中</option>
        <option value="2">已关闭</option>
    </select>
  </div>
  <div class="col-xs-12 col-sm-offset-4 col-sm-4" style="margin-top:10px;">
    <button type="submit" class="btn btn-lg btn-info">搜索</button>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-top:10px;">
    <button type="button" class="btn btn-lg btn-primary" onclick="javascript:location.href='commu_content.php';">新主题</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
<table class="table table-striped">
<thead>
  <tr>
    <th>代理商</th>
    <th>医院</th>
    <th>主题</th>
    <th>创建时间</th>
    <th>下次计划</th>
    <th>状态</th>
    <th>查看</th>
  </tr>
</thead>
<tbody>$htmlCommu</tbody>
</table>
EOF;
require 'tpl/footer.tpl';
