<?php
require '../common.php';
include_head('删除单条心电');

$guardianId = $_GET['id'];
$ecgId = $_GET['eid'];

$ret = Dbi::getDbi()->delEcg($ecgId);
if (VALUE_DB_ERROR === $ret) {
    echo '处理失败，请重试或联系管理员。';
} else {
    echo '处理成功。';
}

user_goto(null, GOTO_FLAG_URL, 'ecg_list.php?id=' . $guardianId);
?>
</html>