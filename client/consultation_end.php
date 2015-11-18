<?php
require '../common.php';
include_head('完成会诊');

$consultationId = $_GET['id'];
Dbi::getDbi()->flowConsultationEnd($consultationId);

user_goto(null, GOTO_FLAG_URL, $_SERVER['HTTP_REFERER']);
?>
</html>