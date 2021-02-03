<?php
header("Content-Type:text/html;charset=utf-8");
require '../config/config.php';
require_once PATH_LIB . 'Logger.php';

if (!isset($_GET['entry']) || empty($_GET['entry'])) {
    echo 'Permission denied!';
    exit;
}

$_GET['entry'] = str_replace('test_', 'client_', $_GET['entry']);

//addApiCounter($_GET['entry']);

if ($_GET['entry'] == 'special_get_patients'
        || $_GET['entry'] == 'sms'
        || $_GET['entry'] == 'clear_real_time_file') {
    $file = $_GET['entry'] . '.php';
} elseif ($_GET['entry'] == 'app_set_command' 
        || $_GET['entry'] == 'app_set_param' 
        || $_GET['entry'] == 'client_update_param') {
    $file = 'set_command.php';
} else{
    $file = get_file($_GET['entry']);
}
if (!file_exists($file)) {
    echo 'Permission denied!';
    exit;
}

foreach ($_GET as $key => $value) {
    $_GET[$key] = trim($value);
}
foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}

$params = $_GET;
foreach ($_POST as $postKey => $postValue) {
    if (!empty($postKey) && $postKey != 'data' && $_GET['entry'] != 'app_upload_data') {
        $params[$postKey] = $postValue;
    }
}
Logger::writeByHour('all_params.log', var_export($params, true));

include $file;

function get_file($api)
{
    $route = explode('_', $api, 2);
    return $route[0] . DIRECTORY_SEPARATOR . $route[1] . '.php';
}

function api_exit(array $ret)
{
    if (empty($ret)) {
        $ret = ['code' => '99', 'message' => '发生未知错误，请联系管理员。'];
    }
    echo json_encode($ret);
    exit;
}

function api_exit_success($otherMsg = '')
{
    api_exit(['code' => '0', 'message' => MESSAGE_SUCCESS . $otherMsg]);
}

function setRegistNotice($hospitalId, $mode = '2')
{
    $file = PATH_CACHE_REGIST_NOTICE . $hospitalId . '.php';
    if (!file_exists($file)) {
        file_put_contents($file, $mode);
    } else {
        $oldArray = explode(',', file_get_contents($file));
        $oldArray[] = $mode;
        $newArray = array_unique($oldArray);
        file_put_contents($file, implode(',', $newArray));
    }
}

function setNotice($hospital, $type, $guardianId = '')
{
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $hospital . '.php';
    
    if ($guardianId == '') {
        if (!file_exists($file)) {
            file_put_contents($file, '1');
        }
        return;
    }
    
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

function clearNotice($hospital, $type, $guardianId)
{
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $hospital . '.php';

    if (!file_exists($file)) {
        return;
    }
    
    include $file;
    $newPatients = array();
    foreach ($patients as $p) {
        if ($p != $guardianId) {
            $newPatients[] = $p;
        }
    }
    
    if (empty($newPatients)) {
        unlink($file);
        return;
    }
    
    $template = "<?php\n";
    $template .= '$patients = array();' . "\n";
    foreach ($newPatients as $patient) {
        $template .= "\$patients[] = '$patient';\n";
    }
    $template .= "\n";
    
    $handle = fopen($file, 'w');
    fwrite($handle, $template);
    fclose($handle);
}

function addApiCounter($entry)
{
    if ($entry == 'app_upload_data1') {
        return;
    }
    $file = PATH_DATA . 'api_counter' . DIRECTORY_SEPARATOR . date('Ymd') . '.php';
    include $file;
    if (isset($api[$entry])) {
        $api[$entry] = $api[$entry] + 1;
    } else {
        $api[$entry] = 1;
    }
    $template = "<?php\n";
    $template .= '$api = array();' . "\n";
    foreach ($api as $key => $value) {
        $template .= "\$api['$key'] = $value;\n";
    }
    file_put_contents($file, $template);
}

function setPatient($id, array $data)
{
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'patient' . DIRECTORY_SEPARATOR . $id . '.php';
    if (file_exists($file)) {
        include_once $file;
    } else {
        $patient = array();
    }
    foreach ($data as $key => $value) {
        $patient[$key] = $value;
    }
    $template = "<?php\n";
    $template .= "\$patient = array();\n";
    foreach ($patient as $key => $value) {
        $template .= "\$patient['$key'] = '$value';\n";
    }
    file_put_contents($file, $template);
}
function getPatient($id)
{
    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'patient' . DIRECTORY_SEPARATOR . $id . '.php';
    if (file_exists($file)) {
        include $file;
    } else {
        $patient = array();
    }
    return $patient;
}
