<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '业务员开单统计';
require 'header.php';

$currentSalesman = isset($_GET['name']) ? $_GET['name'] : '';
$startTime = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : null;
$endTime = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] . ' 23:59:59' : null;
$startTimeDisplay = isset($_GET['start_time']) && !empty($_GET['start_time']) ? $_GET['start_time'] : '';
$endTimeDisplay = isset($_GET['end_time']) && !empty($_GET['end_time']) ? $_GET['end_time'] : '';

$ret = DbiAdmin::getDbi()->getSalesmanList();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$htmlSalesman = '<option value="0">请选择业务员</option>';
foreach ($ret as $value) {
    if ($currentSalesman == $value['salesman']) {
        $htmlSalesman .= '<option value="' . $value['salesman'] . '" selected>' . $value['salesman'] . '</option>';
    } else {
        $htmlSalesman .= '<option value="' . $value['salesman'] . '">' . $value['salesman'] . '</option>';
    }
}

$ret = DbiAdmin::getDbi()->getSalesmanData($currentSalesman, $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$count = count($ret);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$rows = 20;
$offset = ($page - 1) * $rows;
$lastPage = ceil($count / $rows);

if (1 === $page) {
    $ret = array_slice($ret, 0, $rows);
} else {
    $ret = DbiAdmin::getDbi()->getSalesmanData($currentSalesman, $startTime, $endTime, $offset, $rows);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
}

if (empty($ret)) {
    $htmlData = '没有数据。';
    $paging = '';
} else {
    $htmlData = '<table class="table table-striped">
    <thead>
      <tr>
        <th>医院名</th>
        <th>病人姓名</th>
        <th>时间</th>
        <th>开单医生</th>
      </tr>
    </thead>
    <tbody>';
    foreach ($ret as $value) {
        $htmlData .= '<tr><td>'
        . $value['hospital_name'] . '</td><td>'
        . $value['patient_name'] . '</td><td>'
        . $value['regist_time'] . '</td><td>'
        . $value['doctor_name'] . '</td></tr>';
    }
    $htmlData .= '</tbody></table>';
    $currentPage = null;
    if (null !== $currentSalesman) {
        $currentPage = "salesman_data.php?name=$currentSalesman&start_time=$startTimeDisplay&end_time=$endTimeDisplay";
    }
    $paging = getPaging($page, $lastPage, $currentPage);
}

$ret = DbiAdmin::getDbi()->getSalesmanData($currentSalesman, $startTime, $endTime);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
if (empty($ret)) {
    $totalData = 0;
    $realData = 0;
} else {
    $totalData = count($ret);
    $realData = 0;
    for ($i = 0; $i < $totalData; $i++) {
        if ($i == 0) {
            $realData++;
            continue;
        }
        if ($ret[$i]['patient_name'] == $ret[$i - 1]['patient_name'] 
                && abs(strtotime($ret[$i]['regist_time']) - strtotime($ret[$i - 1]['regist_time'])) < 86400) {
            //do nothing.
        } else {
            $realData++;
        }
        continue;
    }
}

echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-1" style="margin-bottom:3px;">
    <label for="salesman" class="control-label"><font color="red">*</font>业务员</label>
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <select class="form-control" name="name">$htmlSalesman</select>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <label for="start_time" class="control-label"><font color="red">*</font>开始日：</label>
    <input type="text" name="start_time" value="$startTimeDisplay" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:3px;">
    <label for="end_time" class="control-label"><font color="red">*</font>结束日：</label>
    <input type="text" name="end_time" value="$endTimeDisplay" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-1">
    <button type="submit" class="btn btn-sm btn-info">查看</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge blue;" />
<div class="row">
<div class="col-xs-12 col-sm-6" style="text-align:left;">
    <label class="control-label">总单数：<font color="red">$totalData</font></label>
</div>
<div class="col-xs-12 col-sm-6" style="text-align:left;">
    <label class="control-label">有效单数：<font color="red">$realData</font></label>
</div>
</div>
<hr style="border-top:1px ridge blue;" />
$htmlData
<div style="text-align:right;">
<ul class="pagination">$paging</ul>
<div>
<script type="text/javascript" src="js/adddate.js"></script>
EOF;
require 'tpl/footer.tpl';
