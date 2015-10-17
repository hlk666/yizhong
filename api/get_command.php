<?php
require '../config/path.php';
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'RemoteCommand.php';

/*
if (!RemoteCommand::validate($_GET)) {
    echo RemoteCommand::getError();
    exit;
}


//@todo change to use cache or file.

$rows = RemoteCommand::getStatus($_GET['id']);
if (VALUE_COMMON_ERROR == $rows) {
    echo RemoteCommand::getError();
    exit;
}

if (empty($rows)) {
    $status = RemoteCommand::CMD_STATUS_NULL;
} else {
    $status = $rows[0]['status'];
}

//if new command exists on current request, reset it to 'not need'.
if ($status == RemoteCommand::CMD_STATUS_EXIST) {
    $ret = RemoteCommand::delCommand($_GET['id']);
    if (VALUE_COMMON_ERROR == $ret) {
        echo json_encode(RemoteCommand::getError());
        exit;
    }
}

echo $status;
*/

function validate()
{
    if (!isset($_GET['id'])) {
        return false;
    }

    $id = trim($_GET['id']);
    if (empty($id)) {
        return false;
    }

    return $_GET;
}
$data = validate();
if (false === $data) {
    echo 'param error.';
    exit;
}
$file = PATH_CACHE_CMD . $data['id'] . '.php';
$result = array();
if (file_exists($file)) {
    include $file;
    if (isset($command['end_time'])) {
        if (time() >= $command['end_time']) {
            $commandNew['action'] = 'end';
        }
    }
    $result['code'] = 0;
    $result['command'] = $commandNew;
    
    //@todo delete new command here.
} else {
    $result['code'] = 9;
}
echo json_encode($result);
