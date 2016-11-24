<?php
require_once PATH_LIB . 'db/DbiYlt.php';

$name = isset($_GET['name']) ? trim($_GET['name']) : null;
$tel = isset($_GET['tel']) ? trim($_GET['tel']) : null;
$identityCard = isset($_GET['identity_card']) ? trim($_GET['identity_card']) : null;
$startTime = isset($_GET['start_time']) ? trim($_GET['start_time']) : null;
$endTime = isset($_GET['end_time']) ? trim($_GET['end_time']) : null;

if (!isValue($identityCard) && !(isValue($name) && isValue($tel))) {
    $data = ['code' => 1, 'message' => '查询条件不足，请提供参数：name。'];
    gotoExit($data);
}
if (null != $startTime && !isTime($startTime)) {
    $data = ['code' => 2, 'message' => '参数类型或格式错误：start_time。'];
    gotoExit($data);
}
if (null != $endTime && !isTime($endTime)) {
    $data = ['code' => 2, 'message' => '参数类型或格式错误：end_time。'];
    gotoExit($data);
}

$guardians = DbiYlt::getDbi()->getGuardians($identityCard, $name, $tel);
if (VALUE_DB_ERROR === $guardians) {
    $data = ['code' => 3, 'message' => '数据库错误，请联系管理员。'];
    gotoExit($data);
}

if (empty($guardians)) {
    $data = ['code' => 4, 'message' => '没有符合条件的结果。'];
    gotoExit($data);
}
$patientName = $guardians[0]['patient_name'];
$guardianStr = '(';
foreach ($guardians as $guardian) {
    $guardianStr .= $guardian['guardian_id'] . ',';
}
$guardianStr = substr($guardianStr, 0, -1) . ')';

$ecgs = DbiYlt::getDbi()->getEcgs($guardianStr, $startTime, $endTime);
if (VALUE_DB_ERROR === $ecgs) {
    $data = ['code' => 3, 'message' => '数据库错误，请联系管理员。'];
    gotoExit($data);
}
if (empty($ecgs)) {
    $data = ['code' => 4, 'message' => '没有符合条件的结果。'];
    gotoExit($data);
}
foreach ($ecgs as $k => $v) {
    unset($ecgs[$k]['ecg_id']);
}

$data = ['code' => 0, 'message' => MESSAGE_SUCCESS, 'patient_name' => $patientName, 'ecgs' => $ecgs];
gotoExit($data);

function isTime($value)
{
    $pattern = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';
    if (preg_match($pattern, $value)) {
        return true;
    }
    return false;
}
function isValue($value)
{
    if (null === $value) {
        return false;
    } else if ('' == $value) {
        return false;
    } else if ('null' == strtolower($value)) {
        return false;
    } else {
        return true;
    }
}
function gotoExit(array $data = ['code' => 99, 'message' => '未知错误，请联系管理员。'])
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}