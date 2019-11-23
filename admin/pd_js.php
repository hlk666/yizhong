<?php
require '../config/config.php';
require '../lib/function.php';
require '../lib/DbiAdmin.php';

$title = '后台处理';
$isHideSider = true;
require 'header.php';

if (empty($_GET['id']) || empty($_GET['type'])) {
    user_back_after_delay('非法访问。');
}
//$version = isset($_GET['version']) && !empty($_GET['version']) ? $_GET['version'] : '';
$iccid = isset($_GET['iccid']) && !empty($_GET['iccid']) ? $_GET['iccid'] : '';

$vPhone = isset($_GET['ver_phone']) && !empty($_GET['ver_phone']) ? $_GET['ver_phone'] : '';
$vEmbedded = isset($_GET['ver_embedded']) && !empty($_GET['ver_embedded']) ? $_GET['ver_embedded'] : '';
$vApp = isset($_GET['ver_app']) && !empty($_GET['ver_app']) ? $_GET['ver_app'] : '';
$vPcb = isset($_GET['ver_pcb']) && !empty($_GET['ver_pcb']) ? $_GET['ver_pcb'] : '';
$vBox = isset($_GET['ver_box']) && !empty($_GET['ver_box']) ? $_GET['ver_box'] : '';

$func = 'pd' . ucwords($_GET['type']);
$ret = DbiAdmin::getDbi()->$func($_GET['id'], $_SESSION['user'], $iccid, $vPhone, $vEmbedded, $vApp, $vPcb, $vBox);
if (VALUE_DB_ERROR === $ret) {
    user_back_after_delay(MESSAGE_DB_ERROR);
}

user_back_after_delay('操作成功。', 2000, 'pd.php');

require 'tpl/footer.tpl';