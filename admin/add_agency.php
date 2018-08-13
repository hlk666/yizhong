<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '添加新的代理商';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'add_agency.php');
    }
    
    $name = isset($_POST['name']) ?  trim($_POST['name']) : '';
    $tel = isset($_POST['tel']) ? trim($_POST['tel']) : '';
    
    if (empty($name)) {
        user_back_after_delay('请正确输入代理商名字。');
    }
    if (empty($tel)) {
        user_back_after_delay('请输入代理商电话。');
    }
    $telDB = DbiAdmin::getDbi()->getAgencyByName($name);
    if (VALUE_DB_ERROR === $telDB) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    if (!empty($telDB)) {
        if ($tel == $telDB) {
            user_back_after_delay("已存在相同姓名和电话的代理商，请勿重复添加。");
        } else {
            user_back_after_delay("已存在相同姓名的代理商，电话是【 $telDB 】。请修改代理商名字后再添加。");
        }
    }
    
    $ret = DbiAdmin::getDbi()->addAgency($name, $tel);
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
    <label class="col-sm-2 control-label">代理商名字<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" placeholder="请输入代理商名字" required>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">代理商电话<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="tel" placeholder="请输入代理商电话" required>
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
