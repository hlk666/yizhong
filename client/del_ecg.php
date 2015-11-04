<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$guardianId = $_GET['id'];
$ecgId = $_GET['eid'];

$ret = Dbi::getDbi()->delEcg($ecgId);
if (VALUE_DB_ERROR == $ret) {
    echo '处理失败，请重试或联系管理员。';
} else {
    echo '处理成功。';
}

header('location:ecg_history.php?id=' . $guardianId);
