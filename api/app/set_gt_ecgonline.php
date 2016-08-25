<?php
require_once PATH_LIB . 'Validate.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($data['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}
if (false === Validate::checkRequired($data['client_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'client_id.']);
}

$hospitalId = $data['hospital_id'];
$clientId = $data['client_id'];

$file = PATH_CACHE_ECGONLINE . $hospitalId . '.php';
if (file_exists($file)) {
    include $file;
} else {
    $clientIdList = array();
}

if (!in_array($clientId, $clientIdList)) {
    $clientIdList[] = $clientId;
    $template = "<?php\n";
    foreach ($clientIdList as $value) {
        $template .= "\$clientIdList[] = '$value';\n";
    }
    
    $handle = fopen($file, 'w');
    fwrite($handle, $template);
    fclose($handle);
}

api_exit_success();
