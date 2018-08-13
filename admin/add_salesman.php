<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '添加新的业务员';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'add_salesman.php');
    }
    
    $name = isset($_POST['name']) ?  trim($_POST['name']) : '';
    //$tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
    
    if (empty($name)) {
        user_back_after_delay('请正确输入业务员姓名。');
    }
    /*
    if (empty($tel)) {
        user_back_after_delay('请输入代理商电话。');
    }
    */
    $ret = DbiAdmin::getDbi()->getSalesmanByName($name);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (!empty($ret)) {
        user_back_after_delay("已存在相同姓名的业务员，请修改业务员姓名后再添加。");
    }
    
    $ret = DbiAdmin::getDbi()->addSalesman($name);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    $_SESSION['post'] = true;
    echo MESSAGE_SUCCESS;
} else {
    $_SESSION['post'] = false;
    
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label">业务员姓名<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" placeholder="业务员姓名" required>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
      <button type="button" class="btn btn-lg btn-primary" style="margin-left:50px" 
        onclick="javascript:history.back();">返回</button>
    </div>
  </div>
</form>
EOF;
}
require 'tpl/footer.tpl';
