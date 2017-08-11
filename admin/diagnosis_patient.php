<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAnalytics.php';

$title = '根据病症查询病人';
require 'header.php';

$startTime = isset($_POST['start_time']) && !empty($_POST['start_time']) ? $_POST['start_time'] : '';
$endTime = isset($_POST['end_time']) && !empty($_POST['end_time']) ? $_POST['end_time'] . ' 23:59:59' : '';
$startTimeDisplay = isset($_POST['start_time']) && !empty($_POST['start_time']) ? $_POST['start_time'] : '';
$endTimeDisplay = isset($_POST['end_time']) && !empty($_POST['end_time']) ? $_POST['end_time'] : '';

$diagnosisChecked = isset($_POST['diagnosis']) ? $_POST['diagnosis'] : array();
$diagnosisMst = ['1' => '病A', '2' => '病B', '3' => '病C'];
$htmlCheckBox = '';
foreach ($diagnosisMst as $key => $value) {
    $checkFlag = in_array($key, $diagnosisChecked) ? ' checked' : '';
    $htmlCheckBox .= '<label class="checkbox-inline"><input type="checkbox" name="diagnosis[]" value="'
            . $key . '"' . $checkFlag . '>' . $value . '</label>';
}

$diagnosisList = '(';
foreach ($diagnosisChecked as $d) {
    $diagnosisList .= $d . ',';
}
if ($diagnosisList == '(') {
    $patients = array();
} else {
    $diagnosisList = substr($diagnosisList, 0, -1) . ')';
    $patients = DbiAnalytics::getDbi()->getPatientByDiagnosis($diagnosisList);
    if (VALUE_DB_ERROR === $patients) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    foreach ($patients as $key => $row) {
        $patients[$key]['age'] = date('Y') - $row['birth_year'];
        $patients[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
        unset($patients[$key]['birth_year']);
        
        $lastHos = DbiAnalytics::getDbi()->getPatientLastHospital($row['patient_id'], $startTime, $endTime);
        if (VALUE_DB_ERROR === $lastHos) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
        if (empty($lastHos)) {
            unset($patients[$key]);
        } else {
            $patients[$key]['hospital_name'] = $lastHos['hospital_name'];
            $patients[$key]['regist_time'] = $lastHos['regist_time'];
        }
    }
}

if (empty($patients)) {
    $htmlData = '没有数据。';
} else {
    $htmlData = '<table class="table table-striped">
    <thead>
      <tr>
        <th>姓名</th>
        <th>性别</th>
        <th>年龄</th>
        <th>电话</th>
        <th>最近一次就诊医院</th>
        <th>最近一次就诊时间</th>
      </tr>
    </thead>
    <tbody>';
    foreach ($patients as $value) {
        $htmlData .= '<tr><td>' 
                . $value['patient_name'] . '</td><td>' 
                . $value['sex'] . '</td><td>' 
                . $value['age'] . '</td><td>'
                . $value['tel'] . '</td><td>'
                . $value['hospital_name'] . '</td><td>'
                . $value['regist_time'] .'</td></tr>';
    }
    $htmlData .= '</tbody></table>';
}

echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  
<div class="row">
  <div class="col-xs-12 col-sm-6" style="margin-bottom:3px;">
    <label for="start_time" class="control-label">开始日：</label>
    <input type="text" name="start_time" value="$startTimeDisplay" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-6" style="margin-bottom:3px;">
    <label for="end_time" class="control-label">结束日：</label>
    <input type="text" name="end_time" value="$endTimeDisplay" onclick="SelectDate(this,'yyyy-MM-dd')" />
  </div>
  <div class="col-xs-12 col-sm-2">
    <label for="salesman" class="control-label"><font color="red">*</font>选择病症</label>
  </div>
  <div class="col-xs-12 col-sm-8">$htmlCheckBox</div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-md btn-info">查看</button>
  </div>
</div>
</form>
<hr style="border-top:1px ridge blue;" />
$htmlData
<script type="text/javascript" src="js/adddate.js"></script>
EOF;
require 'tpl/footer.tpl';
