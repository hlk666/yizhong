<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '配置长程分析医院';
require 'header.php';

if (isset($_POST['edit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要重复刷新页面。', 2000, 'edit_tree.php');
    }
    
    $hospitalId = !isset($_POST['hospital_id']) ? null : $_POST['hospital_id'];
    $analysisHospital = !isset($_POST['hospital_analysis']) ? null : $_POST['hospital_analysis'];
    $reportHospital = !isset($_POST['hospital_report']) ? null : $_POST['hospital_report'];
    $title1 = !isset($_POST['title1']) ? null : $_POST['title1'];
    $title2 = !isset($_POST['title2']) ? null : $_POST['title2'];
    
    if (empty($hospitalId)) {
        user_back_after_delay('非法访问');
    }
    if (empty($analysisHospital)) {
        user_back_after_delay('请选择分析医院。');
    }
    if (empty($reportHospital)) {
        user_back_after_delay('请选择出报告医院。');
    }
    if (empty($title1)) {
        user_back_after_delay('请选择抬头医院1。');
    }
    
    $ret = DbiAdmin::getDbi()->editTree($hospitalId, $analysisHospital, $reportHospital, $title1, $title2);
    
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS
    . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" '
            . ' onclick="javascript:location.href=\'hospital.php\';">查看医院列表</button>';
} else {
    $hospitalId = isset($_GET['id']) ? $_GET['id'] : null;
    $_SESSION['post'] = false;
    
    if (empty($hospitalId)) {
        user_back_after_delay('非法访问');
    }
    $hospitalInfo = DbiAdmin::getDbi()->getHospitalInfo($hospitalId);
    if (VALUE_DB_ERROR === $hospitalInfo) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $hospitalName = $hospitalInfo['hospital_name'];
    
    $hospitalTree = DbiAdmin::getDbi()->getHospitalTree($hospitalId);
    if (VALUE_DB_ERROR === $hospitalTree) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (empty($hospitalTree)) {
        $analysisHospital = '0';
        $reportHospital = '0';
        $title1 = '0';
        $title2 = '0';
    } else {
        $analysisHospital = $hospitalTree['analysis_hospital'];
        $reportHospital = $hospitalTree['report_hospital'];
        $title1 = $hospitalTree['title1'];
        $title2 = $hospitalTree['title2'];
    }
    
    $ret = DbiAdmin::getDbi()->getHospitalListHigh($hospitalId);
    if (VALUE_DB_ERROR === $ret) {
        $ret = array();
    }
    $htmlAnalysisHospitals = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $analysisHospital) {
            $htmlAnalysisHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlAnalysisHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    
    $htmlReportHospitals = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $reportHospital) {
            $htmlReportHospitals .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlReportHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    
    $htmlTitle1 = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $title1) {
            $htmlTitle1 .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlTitle1 .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    $htmlTitle2 = '';
    foreach ($ret as $value) {
        if ($value['hospital_id'] == $title2) {
            $htmlTitle2 .= '<option value="' . $value['hospital_id'] . '" selected>' . $value['hospital_name'] . '</option>';
        } else {
            $htmlTitle2 .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    if (in_array($_SESSION['user'], $auth_level1)) {
        $authEditTreeSubmit = '<button type="submit" class="btn btn-lg btn-info" name="edit">修改</button>';
    } else {
        $authEditTreeSubmit = '';
    }
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <input type="hidden" name="hospital_id" value="$hospitalId">
  <div class="form-group">
    <label class="col-sm-2 control-label">当前医院</label>
    <label class="col-sm-10 control-label" style="text-align:left;">$hospitalName</label>
  </div>
  <div class="form-group">
    <label for="hospital_analysis" class="col-sm-2 control-label">分析医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital_analysis">
        <option value="0">请选择分析医院</option>$htmlAnalysisHospitals
    </select></div>
  </div>
  <div class="form-group">
    <label for="hospital_report" class="col-sm-2 control-label">出报告医院</label>
    <div class="col-sm-10">
      <select class="form-control" name="hospital_report">
        <option value="0">请选择出报告医院</option>$htmlReportHospitals
    </select></div>
  </div>
  <div class="form-group">
    <label for="title1" class="col-sm-2 control-label">抬头医院1(签名)</label>
    <div class="col-sm-10">
      <select class="form-control" name="title1">
        <option value="0">请选择抬头医院1</option>$htmlTitle1
    </select></div>
  </div>
  <div class="form-group">
    <label for="title2" class="col-sm-2 control-label">抬头医院2(双抬头)</label>
    <div class="col-sm-10">
      <select class="form-control" name="title2">
        <option value="0">请选择抬头医院2</option>$htmlTitle2
    </select></div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      $authEditTreeSubmit
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px"
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
EOF;
}

require 'tpl/footer.tpl';
