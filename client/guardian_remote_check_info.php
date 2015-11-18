<?php
require '../common.php';
require PATH_LIB . 'Invigilator.php';
include_head('远程查房');

$guardianId = $_GET['id'];

$data = array('check_info' => 'on');
$invigilator = new Invigilator($guardianId);
$invigilator->create($data);

user_back_after_delay('已经发送远程查房命令，请等待数据上传后查看(约1分钟)。', 1500);
?>
</html>