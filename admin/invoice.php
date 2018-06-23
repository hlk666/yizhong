<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '发票信息';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。');
    }
    
    $hospitalId = !isset($_POST['hospital']) ? 0 : $_POST['hospital'];
    $invoiceEndDate = !isset($_POST['end_date']) ? null : $_POST['end_date'];
    
    if (empty($hospitalId)) {
        user_back_after_delay('请选择医院。');
    }

    if (empty($invoiceEndDate)) {
        user_back_after_delay('请选择开票截止日期。');
    }
    $ret = DbiAdmin::getDbi()->editInvoiceEndDate($hospitalId, $invoiceEndDate);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS;
} else {
    $_SESSION['post'] = false;
    $name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : '';
    echo <<<EOF
<form class="form-horizontal" role="form" method="get">
<div class="row">
  <div class="col-xs-12 col-sm-2" style="margin-bottom:3px;">
    <label class="control-label">医院名(模糊匹配)</label>
  </div>
  <div class="col-xs-12 col-sm-3" style="margin-bottom:3px;">
    <input type="text" class="form-control" name="name" value="$name" >
  </div>
  <div class="col-xs-12 col-sm-2">
    <button type="submit" class="btn btn-info">显示</button>
  </div>
</div>
</form>
EOF;
    if (empty($name)) {
        $htmlHospitals = '<option value="0">无医院</option>';
    } else {
        $ret = DbiAdmin::getDbi()->getHospitalList(null, null, null, $name);
        if (VALUE_DB_ERROR === $ret) {
            $ret = array();
        }
        $htmlHospitals = '<option value="0">请选择医院</option>';
        foreach ($ret as $value) {
            $htmlHospitals .= '<option value="' . $value['hospital_id'] . '">' . $value['hospital_name'] . '</option>';
        }
    }
    echo <<<EOF
<hr style="border-top:1px ridge red;" />
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label for="hospital" class="col-sm-2 control-label">请选择医院：<font color="red">*</font></label>
    <div class="col-sm-10"><select class="form-control" name="hospital" onchange="getHosInvoice(this.options[this.options.selectedIndex].value)">
    $htmlHospitals</select></div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">当前开票截止日期：</label>
    <label class="col-sm-6 control-label" id="current_invoice" style="text-align:left;color:red;">未选择医院</label>
  </div>
  <div class="form-group">
    <label for="device_id" class="col-sm-2 control-label">新的开票截止日期<br />(格式：2018-01-01)<font color="red">*</font></label>
    <div class="col-sm-10"><input type="text" name="end_date" class="form-control" onclick="SelectDate(this,'yyyy-MM-dd')" /></div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-5 col-sm-2">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
    </div>
  </div>
</form>
<script type="text/javascript" src="js/adddate.js"></script>
EOF;
}
require 'tpl/footer.tpl';
