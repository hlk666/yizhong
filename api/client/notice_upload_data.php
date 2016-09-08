<?php
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_ROOT . 'lib/tool/HpMessage.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['upload_url'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'upload_url.']);
}

$guardianId = $_POST['patient_id'];
$url = $_POST['upload_url'];

$ret = DbiAnalytics::getDbi()->addGuardianData($guardianId, $url);
if (VALUE_DB_ERROR === $ret) {
    api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
}

$tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
if (VALUE_DB_ERROR === $tree || array() == $tree) {
    //do nothing.
} else {
    $vc = createVC($guardianId);
    HpMessage::sendTelMessage("病人(id:$guardianId, 验证码:$vc)的数据文件已经上传完毕，请下载、分析。", $tree['analysis_hospital']);
}

api_exit_success();

function createVC($guardianId)
{
    $allText = '1234567890';
    $vc = '';
    for ($i = 1; $i <= 4; $i++) {
        $index = rand(0,9);
        $vc .= substr($allText, $index, 1);
    }
    
    $vcFile = PATH_ROOT . 'VerificationCode' . DIRECTORY_SEPARATOR . $guardianId . '.php';
    $template = "<?php\n";
    $template .= '$rightVC = ' . $vc . ";\n";
    file_put_contents($vcFile, $template);
    
    return $vc;
}
