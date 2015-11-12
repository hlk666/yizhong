<?php
require '../config/path.php';
require PATH_LIB . 'Invigilator.php';

$guardianId = $_GET['id'];

$data = array('check_info' => 'on');
$invigilator = new Invigilator($guardianId);
$invigilator->create($data);

echo '已经发送远程查房命令，请等待数据上传后查看(1分钟)。';
echo '<script language="javascript">setTimeout("history.back()", 1500);</script>';
exit;
