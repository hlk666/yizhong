<?php
require '../common.php';
include_head('删除单条心电');

$guardianId = $_GET['id'];
$ecgId = $_GET['eid'];

$ret = Dbi::getDbi()->delEcg($ecgId);
if (VALUE_DB_ERROR === $ret) {
    echo MESSAGE_DB_ERROR;
} else {
    echo MESSAGE_SUCCESS;
}

user_goto(null, GOTO_FLAG_URL, 'ecg_list.php?id=' . $guardianId);
?>
</html>