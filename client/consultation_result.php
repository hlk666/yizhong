<?php
require '../common.php';
include_head('回复会诊');

$consultationId = $_GET['cid'];
$result = $_GET['res'];
$ret = Dbi::getDbi()->flowConsultationReply($consultationId, $result);
if (VALUE_DB_ERROR === $ret) {
    echo '处理失败，请重试或联系管理员。';
} else {
    echo '处理成功。';
}
?>
</html>