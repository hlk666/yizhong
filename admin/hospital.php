<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院列表';
require 'header.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';
$salesman = isset($_GET['salesman']) ? $_GET['salesman'] : '';
$name = isset($_GET['name']) ? $_GET['name'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

$ret = DbiAdmin::getDbi()->getSalesmanList();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$htmlSalesman = '<option value="0">请选择业务员</option>';
foreach ($ret as $value) {
    if ($salesman == $value['salesman_id']) {
        $htmlSalesman .= '<option value="' . $value['salesman_id'] . '" selected>' . $value['name'] . '</option>';
    } else {
        $htmlSalesman .= '<option value="' . $value['salesman_id'] . '">' . $value['name'] . '</option>';
    }
}

$typeSelected = '<option value="0">请选择</option> 
    <option value="1"' . ($type == '1' ? ' selected ' : '') . '>云平台</option>
    <option value="2"' . ($type == '2' ? ' selected ' : '') . '>分析中心</option>
    <option value="3"' . ($type == '3' ? ' selected ' : '') . '>下级医院</option>
    <option value="4"' . ($type == '4' ? ' selected ' : '') . '>独立医院</option>';

$levelSelected = '<option value="0">请选择</option>  
    <option value="3"' . ($level == '3' ? ' selected ' : '') . '>三级</option>
    <option value="2"' . ($level == '2' ? ' selected ' : '') . '>二级</option>
    <option value="1"' . ($level == '1' ? ' selected ' : '') . '>一级</option>';

$ret = DbiAdmin::getDbi()->getHospitalList($type, $level, $salesman, $name, 0, null, $id);
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
    $ret = DbiAdmin::getDbi()->getHospitalList($type, $level, $salesman, $name, $offset, $rows, $id);
}

$htmlHospitals = '';
foreach ($ret as $value) {
    if ($value['quantity'] == 0) {
        $link = '0';
    } else {
        $link = '<a href="hospital_device.php?hospital=' . $value['hospital_id'] . '">' . $value['quantity'] . '</a>';
    }
    
    if (in_array($_SESSION['user'], ['hp', 'wxy', 'xks1', 'whl', 'pangx', 'fanzp'])) {
        $authHospitalEditHospital = '<button type="button" class="btn btn-xs btn-warning" onclick="javascript:editHospital(' 
                . $value['hospital_id'] . ')">修改</button></td><td>';
        $authHospitalEditRelation = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:editRelation(' 
                . $value['hospital_id'] . ')">配置</button></td><td>';
        $authHospitalEditTree = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:editTree(' 
                . $value['hospital_id'] . ')">配置</button></td><td>';
        $authHospitalDelete = '<button type="button" class="btn btn-xs btn-danger" onclick="javascript:deleteHospital(' 
                . $value['hospital_id'] . ')">删除</button></td>';
        $authHospitalHeader = '<th>删除医院</th>';
    } else {
        $authHospitalEditHospital = '<button type="button" class="btn btn-xs btn-warning" onclick="javascript:editHospital('
                . $value['hospital_id'] . ')">查看</button></td><td>';
        $authHospitalEditRelation = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:editRelation(' 
                . $value['hospital_id'] . ')">查看</button></td><td>';
        $authHospitalEditTree = '<button type="button" class="btn btn-xs btn-info" onclick="javascript:editTree(' 
                . $value['hospital_id'] . ')">查看</button></td><td>';
        $authHospitalDelete = '';
    }
    
    $htmlHospitals .= '<tr><td>' 
            . $value['hospital_id'] . '</td><td>'
            . $value['hospital_name'] . '</td><td>'
            . $value['login_name'] . '</td><td>'
            . $link . '</td><td>'
            . $authHospitalEditHospital . $authHospitalEditRelation . $authHospitalEditTree . $authHospitalDelete . '</tr>';
}

$currentPage = "hospital.php?type=$type&level=$level&salesman=$salesman&name=$name";
$paging = getPaging($page, $lastPage, $currentPage);

echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">选择定位/类型</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="type">$typeSelected</select>
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">选择医院级别</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="level">$levelSelected</select>
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label for="salesman" class="control-label">业务员</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <select class="form-control" name="salesman">$htmlSalesman</select>
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">医院名</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="name" value="$name">
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">医院id</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="id" value="$id">
  </div>
  <div class="col-xs-12 col-sm-offset-5 col-sm-4" style="margin-top:10px;">
    <button type="submit" class="btn btn-lg btn-info">搜索</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge red;" />
<table class="table table-striped">
<thead>
  <tr>
    <th>ID</th>
    <th>医院名</th>
    <th>管理员用户</th>
    <th>设备数量</th>
    <th>基本信息</th>
    <th>上级医院</th>
    <th>长程分析</th>
    $authHospitalHeader
  </tr>
</thead>
<tbody>$htmlHospitals</tbody>
</table>
<div style="text-align:right;">
<ul class="pagination">$paging</ul>
<div>
EOF;
require 'tpl/footer.tpl';
