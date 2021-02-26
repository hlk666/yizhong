<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';
require_once '../lib/Logger.php';

$title = '纠正开单信息';
require 'header.php';

if (isset($_POST['submit'])){
    if (true === $_SESSION['post']) {
        user_back_after_delay('请不要刷新页面。', 2000, 'edit_regist.php');
    }
    
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        echo '请登录后再操作。';
        header('location:index.php');
        exit;
    }
    $creator = $_SESSION['user'];
    $hospitalId = !isset($_POST['hospital_id']) ? null : $_POST['hospital_id'];
    $guardianId = !isset($_POST['guardian_id']) ? null : $_POST['guardian_id'];

    
    if (empty($hospitalId)) {
        user_back_after_delay('请正确输入医院id。');
    }
    if (empty($guardianId)) {
        user_back_after_delay('请正确输入监护id。');
    }
    
    $ret = DbiAdmin::getDbi()->editRegist($guardianId, $hospitalId);
    if (VALUE_DB_ERROR === $ret) {
        user_back_after_delay(MESSAGE_DB_ERROR);
    }
    
    $_SESSION['post'] = true;
    echo '<h1>添加成功。</h1><a href="patient.php?guardian=' . $guardianId . '">点击查看</a>';
    Logger::write('edit_regist.log', $_SESSION['user'] . '。' . var_export($_POST, true));
} else {
    $_SESSION['post'] = false;
    echo <<<EOF
<form class="form-horizontal" role="form" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label">医院id<font color="red">*</font></label>
    <div class="col-sm-4">
      <input type="text" class="form-control" name="hospital_id" placeholder="请输入正确的医院ID" onchange="getHosName(this.value)" required>
    </div>
    <label class="col-sm-6 control-label" id="title" style="text-align:left;color:red;"></label>
  </div>
  
  <div class="form-group">
    <label class="col-sm-2 control-label">监护id<font color="red">*</font></label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="guardian_id" placeholder="请输入监护ID" required>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10" style="text-align:center;">
      <button type="submit" class="btn btn-lg btn-success" name="submit">保存</button>
    </div>
  </div>
</form>
<script src="js/yizhong.js"></script>
EOF;
}
require 'tpl/footer.tpl';
