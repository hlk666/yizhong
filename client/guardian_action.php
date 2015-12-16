<?php
require '../common.php';
require PATH_LIB . 'Invigilator.php';
include_head('监护处理');

session_start();
checkDoctorLogin();

$guardianId = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
if ($status > 1) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_BACK);
}

if (isset($_POST['action'])) {
    $hospitalId = $_SESSION['hospital'];
    $guardianId = $_POST['id'];
    $action = $_POST['action'];
    
    $command = array();
    $invigilator = new Invigilator($guardianId);
    if (1 == $action) {
        $command = array('action' => 'start');
        $ret = $invigilator->create($command);
        $message = '已经开始本次监护，即将返回前页。';
    }
    if (2 == $action) {
        $command = array('action' => 'end');
        $ret = $invigilator->create($command);
        $message = '已经结束本次监护，即将返回前页。';
    }
    if (0 == $action) {
        $ret = $invigilator->delete();
        $message = '已经放弃本次监护，即将返回前页。';
        
    }
    if (VALUE_DB_ERROR === $ret) {
        $message = '操作失败，请重试。即将返回前页。';
    }
    if (VALUE_GT_ERROR === $ret) {
        $message = '和设备通信失败，请重试。即将返回前页。';
    }
    
    user_back_after_delay($message, 2000, 'patient_list.php?current_flag=1&id=' . $hospitalId);
}
?>
<body>
<form action="" method="post" id="formAction" onsubmit="return myConfirm();">
<input type="hidden" name="id" value="<?php echo $guardianId;?>" />
<div style="padding-bottom:5px;">
<!-- <select name="action" style="width:140px;height:30px;font-size:15px;"> -->
<?php
if (0 == $status) {
    echo '<input type="radio" name="action" value="1" checked="checked" />开始监护';
    echo '<br />';
    echo '<input type="radio" name="action" value="0" />放弃本次监护';
}
if (1 == $status) {
    echo '<input type="radio" name="action" value="2" checked="checked" />结束监护';
    echo '<br />';
}
?>
</div>
<div style="margin-top: 5px;">
<input style="width:80px;height:30px;" name="submit" type="submit" value="执行"/>
<input style="margin-left:20px;width:80px;height:30px;" name="return" type="button" value="返回" 
onclick="javscript:history.back();"/>
</div>
</form>
<script type="text/javascript">
function myConfirm()
{
    var action = document.getElementById("action");
    alert(action.value);
    return false;
}
</script>
</body>
</html>