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
    setUploadNotice($tree['analysis_hospital'], $guardianId);
}

api_exit_success();

function setUploadNotice($hospital, $guardianId)
{
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data' . DIRECTORY_SEPARATOR . $hospital . '.php';
    if (file_exists($file)) {
        include $file;
        $patients[] = $guardianId;
        $patients = array_unique($patients);
    } else {
        $patients = array();
        $patients[] = $guardianId;
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
