<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require_once '../lib/Logger.php';

$title = '修改业务员信息';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要重复刷新页面。', 2000, 'edit_salesman.php');
    }
    
    $name = isset($_POST['name']) ?  trim($_POST['name']) : '';
    $id = isset($_POST['id']) ? trim($_POST['id']) : '';
    
    if (empty($id)) {
        user_back_after_delay('非法访问');
    }
    
    if (empty($name)) {
        user_back_after_delay('请正确输入业务员姓名。');
    }
    
    $isExisted = DbiAdmin::getDbi()->getSalesmanByNameId($id, $name);
    if (VALUE_DB_ERROR === $isExisted) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (true === $isExisted) {
        user_back_after_delay("业务员名<font color='red'>$name</font>和他人冲突，请修改。");
    }
    
    $ret = DbiAdmin::getDbi()->editSalesman($id, $name);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS
    . '<br /><button type="button" class="btn btn-lg btn-info" style="margin-top:50px;" '
            . ' onclick="javascript:location.href=\'salesman.php\';">查看业务员列表</button>';
} else {
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $_SESSION['post'] = false;
    
    if (empty($id)) {
        user_back_after_delay('非法访问');
    }
    
    $salesmanInfo = DbiAdmin::getDbi()->getSalesmanInfo($id);
    if (VALUE_DB_ERROR === $salesmanInfo) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $salesmanName = $salesmanInfo['salesman_name'];
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <input type="hidden" name="id" value="$id">
  <div class="form-group">
    <label class="col-sm-2 control-label">业务员名<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="$salesmanName">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">修改</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px"
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
EOF;
}

require 'tpl/footer.tpl';
