<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '沟通详细信息';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'device_answer.php');
    }
    $agencyId = isset($_POST['agency']) ?  trim($_POST['agency']) : '0';
    $hospitalId = isset($_POST['hospital']) ?  trim($_POST['hospital']) : '0';
    $titleId = isset($_POST['title_id']) ?  trim($_POST['title_id']) : '0';
    $titleText = isset($_POST['title']) ?  trim($_POST['title']) : '';
    $content = isset($_POST['content']) ?  trim($_POST['content']) : '';
    $nextTime = isset($_POST['next_time']) ?  trim($_POST['next_time']) : '';
    $status= isset($_POST['status']) ?  trim($_POST['status']) : '1';
    if (empty($content)) {
        user_back_after_delay('请正确输入沟通信息。');
    }
    
    $outTitleId = DbiAdmin::getDbi()->addCommuTitleContent($agencyId, $hospitalId, $titleId, $titleText, $content, $nextTime, $status);
    if (VALUE_DB_ERROR === $outTitleId) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    
    echo MESSAGE_SUCCESS
    . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" '
            . ' onclick="javascript:location.href=\'commu_content.php?title_id=' . $outTitleId . '\';">刷新查看</button>';

} else {
    $_SESSION['post'] = false;
    
    $id = isset($_GET['title_id']) ? $_GET['title_id'] : '0';
    if (empty($id)) {
        $data = array();
        $htmlAgency = '<input type="text" class="form-control" name="agency" required>';
        $htmlHospital = '<input type="text" class="form-control" name="hospital" required>';
        $htmlTitle = '<input type="text" class="form-control" name="title" required>';
        //$htmlTitleTime = '';
        $htmlNextTime = '<input type="date" name="next_time" value="" />';
        $htmlStatus = '<select class="form-control" name="status"><option value="1" selected>进行中</option><option value="2" >已关闭</option></select>';
        $htmlData = '';
    } else {
        $data = DbiAdmin::getDbi()->getCommuTitleContent($id);
        if (VALUE_DB_ERROR === $data) {
            user_back_after_delay(MESSAGE_DB_ERROR);
        }
        $htmlAgency = '<label class="control-label">' . (empty($data[0]['agency_name']) ? '&#8722' : $data[0]['agency_name']) . '</label>';
        $htmlHospital = '<label class="control-label">' . (empty($data[0]['hospital_name']) ? '&#8722' : $data[0]['hospital_name']) . '</label>';
        $htmlTitle = '<label class="control-label">' . $data[0]['title'] . '</label>';
        //$htmlTitleTime = '<label class="control-label">' . $data[0]['title_time'] . '</label>';
        $htmlNextTime = '<input type="date" name="next_time" value="' . substr($data[0]['next_time'], 0, 10) . '" />';
        $htmlStatus = '<select class="form-control" name="status"><option value="1">进行中</option><option value="2" >已关闭</option></select>';
        $htmlData = '<table class="table table-striped"><thead><tr><th>时间</th><th>沟通内容</th></tr></thead><tbody>';
        foreach ($data as $item) {
            $htmlData .= '<tr><td>' . $item['content_time'] . '</td><td>' . $item['content'] . '</td></tr>';
        }
        $htmlData .= '</tbody></table>';
    }
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
<input type="hidden" name="title_id" value="$id">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:15px;">
    <label class="control-label">代理商：</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:15px;">
    $htmlAgency
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:15px;">
    <label class="control-label">医院：</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:15px;">
    $htmlHospital
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:15px;">
    <label class="control-label">沟通主题：</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:15px;">
    $htmlTitle
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:15px;">
    <label class="control-label">下次计划时间：</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:15px;">
    $htmlNextTime
  </div>
  <div class="col-xs-12 col-sm-2" style="margin-bottom:15px;">
    <label class="control-label">状态：</label>
  </div>
  <div class="col-xs-12 col-sm-4" style="margin-bottom:15px;">
    $htmlStatus
  </div>
</div>
<hr style="border-top:1px ridge red;" />
  $htmlData
  <div class="col-xs-12 col-sm-12" style="margin-bottom:10px;">
    <label class="control-label">录入新的沟通：</label>
  </div>
  <div class="col-xs-12 col-sm-12" style="margin-bottom:10px;">
    <textarea rows="5" class="form-control" name="content" required></textarea>
  </div>
  <div class="col-sm-offset-2 col-sm-10">
    <button type="submit" class="btn btn-lg btn-info" name="submit">登录</button>
    <button type="button" class="btn btn-lg btn-primary" onclick="javascript:history.back();" style="margin-left:50px">返回</button>
  </div>
</form>
EOF;
}
require 'tpl/footer.tpl';
