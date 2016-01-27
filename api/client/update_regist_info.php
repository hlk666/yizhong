<?php
require_once PATH_LIB . 'Dbi.php';
require_once PATH_LIB . 'Validate.php';
require_once PATH_LIB . 'Invigilator.php';

if (false === Validate::checkRequired($_POST['patient_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'patient_id.']);
}
if (false === Validate::checkRequired($_POST['hospital_id'])) {
    api_exit(['code' => '1', 'message' => MESSAGE_REQUIRED . 'hospital_id.']);
}

$device = isset($_POST['device_id']) ? $_POST['device_id'] : null;
$outerPatientId = null;
if (null != $device) {
    $hospital = $_POST['hospital_id'];
    check_device($device, $hospital, $outerPatientId);
}

$guardianId = $_POST['patient_id'];
$name = isset($_POST['name']) ? $_POST['name'] : null;
$age = isset($_POST['age']) ? $_POST['age'] : null;
$sex = isset($_POST['sex']) ? $_POST['sex'] : null;
$tel = isset($_POST['tel']) ? $_POST['tel'] : null;
$tentativeDiagnose = isset($_POST['tentative_diagnose']) ? $_POST['tentative_diagnose'] : null;
$medicalHistory = isset($_POST['medical_history']) ? $_POST['medical_history'] : null;
$doctorName = isset($_POST['doctor_name']) ? $_POST['doctor_name'] : null;
$height = isset($_POST['height']) ? $_POST['height'] : null;
$weight = isset($_POST['weight']) ? $_POST['weight'] : null;
$bloodPressure = isset($_POST['blood_pressure']) ? $_POST['blood_pressure'] : null;
$sickRoom = isset($_POST['sickroom']) ? $_POST['sickroom'] : null;
$familyTel = isset($_POST['family_tel']) ? $_POST['family_tel'] : null;

$dataPatient = array();
if (null != $name) {
    $dataPatient['patient_name'] = $name;
}
if (null != $age) {
    $dataPatient['birth_year'] = date('Y') - $age;
}
if (null != $sex) {
    $dataPatient['sex'] = $sex;
}
if (null != $tel) {
    $dataPatient['tel'] = $tel;
}
if (null != $sickRoom) {
    $dataPatient['address'] = $sickRoom;
}
if (!empty($dataPatient)) {
    if (null == $outerPatientId) {
        $ret = Dbi::getDbi()->getGuardianById($guardianId);
        if (VALUE_DB_ERROR === $ret) {
            api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
        }
        $outerPatientId = $ret['patient_id'];
    }
    $ret = Dbi::getDbi()->editPatient($outerPatientId, $dataPatient);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
}

$dataGuardian = array();
if (null != $device) {
    $dataGuardian['device_id'] = $device;
}
if (null != $tentativeDiagnose) {
    $dataGuardian['tentative_diagnose'] = $tentativeDiagnose;
}
if (null != $medicalHistory) {
    $dataGuardian['medical_history'] = $medicalHistory;
}
if (null != $doctorName) {
    $dataGuardian['regist_doctor_name'] = $doctorName;
}
if (null != $height) {
    $dataGuardian['height'] = $height;
}
if (null != $weight) {
    $dataGuardian['weight'] = $weight;
}
if (null != $bloodPressure) {
    $dataGuardian['blood_pressure'] = $bloodPressure;
}
if (null != $sickRoom) {
    $dataGuardian['sickroom'] = $sickRoom;
}
if (null != $familyTel) {
    $dataGuardian['family_tel'] = $familyTel;
}
if (!empty($dataGuardian)) {
    $ret = Dbi::getDbi()->editGuardian($guardianId, $dataGuardian);
    if (VALUE_DB_ERROR === $ret) {
        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
    }
}

$result = array();
$result['code'] = '0';
$result['message'] = $guardianId;
api_exit($result);

function check_device($device, $hospital, &$outerPatientId)
{
    $ret = Dbi::getDbi()->existedDeviceHospital($device, $hospital);
    if (false == $ret) {
        api_exit(['code' => '5', 'message' => '此设备不属于该医院。']);
    }
    $guardian = Dbi::getDbi()->getGuardianByDevice($device);
    if (VALUE_DB_ERROR === $guardian) {
        api_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
    }
    if (!empty($guardian)) {
        $outerPatientId = $guardian['patient_id'];
        $patient = Dbi::getDbi()->getPatient($guardian['patient_id']);
        if (VALUE_DB_ERROR === $patient) {
            api_exit(['code' => '3', 'message' => MESSAGE_DB_ERROR]);
        }
        if (empty($patient)) {
            $otherPatient = '其他用户(id:' . $guardian['patient_id'] . ')';
        } else {
            $otherPatient = $patient['patient_name'];
        }
        if ('0' == $guardian['status'] || '1' == $guardian['status']) {
            api_exit(['code' => '4', 'message' => $otherPatient . '正在使用该设备。']);
        }
    }
}
