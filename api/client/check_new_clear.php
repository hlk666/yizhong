<?php
require_once PATH_LIB . 'Validate.php';

if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($_POST['type'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'type.']);
}
$hospitalId = $_POST['hospital_id'];
$clearTarget = $_POST['type'];
$guardianId = isset($_POST['patient_id']) ? $_POST['patient_id'] : null;

$fileEcgNotice = PATH_CACHE_ECG_NOTICE . $hospitalId . '.php';
$fileRegistNotice = PATH_CACHE_REGIST_NOTICE . $hospitalId . '.php';
$fileConsultationApply = PATH_CACHE_CONSULTATION_APPLY_NOTICE . $hospitalId . '.php';
$fileConsultationReply = PATH_CACHE_CONSULTATION_REPLY_NOTICE . $hospitalId . '.php';
$fileUploadData = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileUploadDataFail = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'upload_data_fail' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileMoveData = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'move_data' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileHbi = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'hbi' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileReport = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'report' . DIRECTORY_SEPARATOR . $hospitalId . '.php';
$fileDiagnosis = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'diagnosis' . DIRECTORY_SEPARATOR . $hospitalId . '.php';

if ($clearTarget == 'upload_data_fail') {
    unlink($fileUploadDataFail);
}
if ($clearTarget == 'move_data') {
    unlink($fileMoveData);
}
if ($clearTarget == 'upload_data') {
    if (empty($guardianId)) {
        unlink($fileUploadData);
    } else {
        delete_cache_patient($fileUploadData, $guardianId);
    }
}
if ($clearTarget == 'hbi') {
    unlink($fileHbi);
}
if ($clearTarget == 'diagnosis') {
    if (empty($guardianId)) {
        unlink($fileDiagnosis);
    } else {
        delete_cache_patient($fileDiagnosis, $guardianId);
    }
    
}

api_exit_success();

function delete_cache_patient($file, $patient)
{
    if (!file_exists($file)) {
        return;
    }
    include $file;
    $key = array_search($patient, $patients);
    if (false !== $key) {
        unset($patients[$key]);
    }
    if (empty($patients)) {
        unlink($file);
    } else {
        $template = "<?php\n";
        $template .= '$patients = array();' . "\n";
        
        foreach ($patients as $p) {
            $template .= "\$patients[] = '$p';\n";
        }
        $template .= "\n";
        
        $handle = fopen($file, 'w');
        fwrite($handle, $template);
        fclose($handle);
    }
}
