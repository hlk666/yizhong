<?php
$data = array_merge($_GET, $_POST);
if (!isset($data['device_id']) || '' == trim($data['device_id'])) {
    echo json_encode(['code' => 1, 'message' => MESSAGE_REQUIRED . 'device_id']);
    exit;
}
if (!isset($data['client_id']) || '' == trim($data['client_id'])) {
    echo json_encode(['code' => 2, 'message' => MESSAGE_REQUIRED . 'client_id']);
    exit;
}

$devicdId = $data['device_id'];
$file = PATH_CACHE_CLIENT . $devicdId . '.php';
if (file_exists($file)) {
    include $file;
} else {
    $clientId = '';
}

if ($clientId != $data['client_id']) {
    $template = "<?php\n";
    $template .= '$clientId = \'' . $data['client_id'] . "';\n";
    
    $handle = fopen($file, 'w');
    fwrite($handle, $template);
    fclose($handle);
}

echo json_encode(array('code' => '0'));
