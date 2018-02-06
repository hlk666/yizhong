<?php
require '../config/config.php';
require '../lib/DbiAdmin.php';

$device = isset($_GET['device']) ? str_replace('，', ',', $_GET['device']) : '';
$device = explode(',', $device);

$strDevice = '';
$html = '';
foreach ($device as $deviceId) {
    $isExisted = DbiAdmin::getDbi()->existedDevice($deviceId);
    if (VALUE_DB_ERROR === $isExisted) {
        $html = '';
        break;
    } elseif (true === $isExisted) {
        $strDevice .= $deviceId . ',';
    }  else {
        continue;
    }
}
if ($strDevice != '') {
    $html = '设备【' . substr($strDevice, 0, -1) . '】已被其他医院绑定，请联系管理员(13465596133)。';
}
echo '
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title>羿中医疗科技有限公司管理系统</title>
</head>
<body>' . $html . '</html>';
