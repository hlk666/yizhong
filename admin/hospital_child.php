<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '医院的长程分析结构';
require 'header.php';

$hospital = isset($_GET['hospital']) ? $_GET['hospital'] : '';

$ret = DbiAdmin::getDbi()->getHospitalListHigh();
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}
$htmlHospitals = '<option value="0">请选择医院</option>';
foreach ($ret as $value) {
    if ($hospital == $value['hospital_id']) {
        $htmlHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
    } else {
        $htmlHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
    }
}

$ret = DbiAdmin::getDbi()->getDeviceGuardianLow($hospital);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

if (empty($ret)) {
    $htmlData = '没有数据。';
} else {
    $htmlData = '<table class="table table-striped">
    <thead>
      <tr>
        <th>医院ID</th>
        <th>医院名</th>
        <th>分析</th>
        <th>出报告</th>
      </tr>
    </thead>
    <tbody>';
    foreach ($ret as $value) {
        if ($value['analysis_hospital'] == $hospital) {
            $analysis = '√';
        } else {
            $analysis = '-';
        }
        if ($value['report_hospital'] == $hospital) {
            $report = '√';
        } else {
            $report = '-';
        }
        $htmlData .= '<tr><td>' . $value['hospital_id'] . '</td><td>' . $value['hospital_name'] . '</td><td>' 
                . $analysis . '</td><td>' . $report .'</td></tr>';
    }
    $htmlData .= '</tbody></table>';
}

echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2">
    <label class="control-label"><font color="red">*</font>医院</label>
  </div>
  <div class="col-xs-12 col-sm-4">
    <select class="form-control" name="hospital">$htmlHospitals</select>
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-md btn-info">查看</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge blue;" />
$htmlData
EOF;
require 'tpl/footer.tpl';
