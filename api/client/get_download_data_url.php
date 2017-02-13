<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_GET['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_GET['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$guardianId = $_GET['patient_id'];
$hospitalId = $_GET['hospital_id'];

$ret = Dbi::getDbi()->getDownloadData($guardianId);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

if (empty($ret)) {
    api_exit(['code' => '4', 'message' => MESSAGE_DB_NO_DATA]);
}
/*
if (!empty($ret['download_start_time'])) {
    if (empty($ret['download_end_time'])) {
        api_exit(['code' => '21', 'message' => '其他分析人员正在下载，请稍后再试。']);
    } else {
        api_exit(['code' => '22', 'message' => '其他分析人员已接单，请选择其他用户文件。']);
    }
}
*/
$url = $ret['url'];
$deviceType = $ret['device_type'];
$data = ['download_start_time' => date('Y-m-d H:i:s')];
$ret = Dbi::getDbi()->noticeDownloadData($guardianId, $data);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

clearUploadNotice($hospitalId, $guardianId);

$result = array();
$result['code'] = '0';
$result['message'] = MESSAGE_SUCCESS;
$result['url'] = $url;
$result['device_type'] = $deviceType;
api_exit($result);

function clearUploadNotice($hospitalId, $guardianId)
{
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
    if (file_exists($file)) {
        include $file;
        foreach ($patients as $key => $value) {
            if ($value == $guardianId) {
                unset($patients[$key]);
            }
        }
    } else {
        return;
    }
    $template = "<?php\n";
    $template .= '$patients = array();' . "\n";

    foreach ($patients as $patient) {
        $template .= "\$patients[] = '$patient';\n";
    }
    $template .= "\n";

    $handle = fopen($file, 'w');
    fwrite($handle, $template);
    fclose($handle);
}
