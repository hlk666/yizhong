<?php
require '../common.php';
require PATH_LIB . 'Invigilator.php';
include_head('监护处理');

$guardianId = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
if ($status > 1) {
    user_goto(MESSAGE_PARAM, GOTO_FLAG_BACK);
}

if (isset($_POST['action'])) {
    session_start();
    $hospitalId = $_SESSION['hospital'];
    
    $guardianId = $_POST['id'];
    $action = $_POST['action'];
    
    $command = array();
    $invigilator = new Invigilator($guardianId);
    if (1 == $action) {
        $message = '已经开始本次监护，即将返回前页。';
        $command = array('action' => 'start');
        $invigilator->create($command);
    }
    if (2 == $action) {
        $message = '已经结束本次监护，即将返回前页。';
        $command = array('action' => 'end');
        $invigilator->create($command);
    }
    if (9 == $action) {
        $message = '已经放弃本次监护，即将返回前页。';
        $invigilator->delete();
    }
    
    user_back_after_delay($message, 2000, 'patient_list.php?current_flag=1&id=' . $hospitalId);
}
?>
<body>
<form action="" method="post" id="formAction" onsubmit="return myConfirm();">
<input type="hidden" name="id" value="<?php echo $guardianId;?>" />
<select name="action" style="width:140px;height:30px;font-size:15px;">
<?php
if (0 == $status) {
    echo '<option value="1" selected="selected">开始监护 </option>';
}
if (1 == $status) {
    echo '<option value="2" selected="selected">结束监护 </option>';
}
?>
<option value="9">放弃本次监护 </option>
</select>
<input style="width:80px;height:30px;" name="submit" type="submit" value="执行"/>
<input style="margin-left:20px;width:80px;height:30px;" name="return" type="button" value="返回" 
onclick="javscript:history.back();"/>
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