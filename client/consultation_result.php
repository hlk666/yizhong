<?php
require_once '../config/path.php';
require_once '../config/value.php';
require_once PATH_LIB . 'Dbi.php';

$consultationId = $_GET['cid'];
$result = $_GET['res'];
$ret = Dbi::getDbi()->replyConsultation($consultationId, $result);
if (VALUE_DB_ERROR == $ret) {
    echo '处理失败，请重试或联系管理员。';
} else {
    echo '处理成功。';
}
