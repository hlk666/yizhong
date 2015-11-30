<?php
require '../common.php';
include_head('回复会诊');

$consultationId = $_GET['cid'];
$result = $_GET['res'];
$ret = Dbi::getDbi()->flowConsultationReply($consultationId, $result);
if (VALUE_DB_ERROR === $ret) {
    echo MESSAGE_DB_ERROR;
} else {
    echo MESSAGE_SUCCESS;
}
?>
</html>