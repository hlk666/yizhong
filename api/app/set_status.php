<?php
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Logger.php';

$data = array_merge($_GET, $_POST);
if (false === Validate::checkRequired($data['device_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'device_id.']);
}
if (false === Validate::checkRequired($data['phone_power'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'phone_power.']);
}
if (false === Validate::checkRequired($data['collection_power'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'collection_power.']);
}
if (false === Validate::checkRequired($data['bluetooth'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'bluetooth.']);
}
if (false === Validate::checkRequired($data['line'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'line.']);
}

Logger::write('deviceStatus.log', var_export($data, true));

$file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'device_status' . DIRECTORY_SEPARATOR . $data['device_id'] . '.php';

$template = "<?php\n";
$template .= '$phone_power = \'' . $data['phone_power'] . "';\n";
$template .= '$collection_power = \'' . $data['collection_power'] . "';\n";
$template .= '$bluetooth = \'' . $data['bluetooth'] . "';\n";
$template .= '$line = \'' . $data['line'] . "';\n";

$handle = fopen($file, 'w');
fwrite($handle, $template);
fclose($handle);

api_exit_success();
