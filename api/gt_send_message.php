<?php
require PATH_LIB . 'Dbi.php';
require PATH_LIB . 'GeTui.php';

$text = 'READY';
/**
 * 1.device_id
 * 2.device_id_list
 * 3.client_id
 * 4.guardian_id
 * 5.guardian_id list
 */

// if (!isset($_GET['cid']) || empty($_GET['cid'])) {
//     echo json_encode(['code' => 1, 'message' => MESSAGE_REQUIRED . 'cid']);
//     exit;
// }
// $clientId = $_GET['cid'];
// if (false === strpos($clientId, ',')) {
//     GeTui::pushToSingle($clientId, $message);
// }

// $devicdId = $_GET['device_id'];
// $file = PATH_CACHE_CLIENT . $devicdId . '.php';
// if (file_exists($file)) {
//     include $file;
// } else {
//     $clientId = '';
// }
// $ret = GeTui::pushToSingle($clientId, $text);
$list = array('bc724340b6326dd7cdfec88095e0fa7e', 'a9c363cee8839330552b32cb58870775');
$ret = GeTui::pushToList($list, $text);
var_dump($ret);

// $invigilator = new Invigilator($guardianId, $mode);
// $ret = $invigilator->create($data);
// if (VALUE_PARAM_ERROR === $ret) {
//     echo json_encode(['code' => 2, 'message' => MESSAGE_PARAM]);
// } elseif (VALUE_DB_ERROR === $ret) {
//     echo json_encode(['code' => 3, 'message' => MESSAGE_DB_ERROR]);
// } elseif (VALUE_GT_ERROR === $ret) {
//     echo json_encode(['code' => 4, 'message' => MESSAGE_GT_ERROR]);
// } else {
//     echo json_encode(array('code' => '0'));
// }
// function checkClientId($clientId, $isArray = false)
// {
//     if ($isArray) {
//         foreach ($clientId as $cid) {
//             if (empty($cid)) {
//                 return false;
//             }
//         }
//     } else {
//         if (empty($clientId)) {
//             return false;
//         }
//     }
//     return true;
// }