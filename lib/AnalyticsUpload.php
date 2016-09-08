<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'DbiAnalytics.php';

function pushShortMessage($hospitalId, $message)
{
    $ret = DbiAnalytics::getDbi()->getHospitalInfo($hospitalId);
    if (VALUE_DB_ERROR === $ret) {
        return;
    }
    $tel = $ret['sms_tel'];
    if ('0' == $tel) {
        return;
    }
    ShortMessageService::send($tel, $message);
}

class AnalyticsUpload
{
    private $error = array();
    private $logFile = 'analyticsLog.txt';
    private $retSuccess = array('code' => 0, 'message' => '');
    
    public function run($param, $data)
    {
        $ret = $this->validate($param, $data);
        if (false === $ret) {
            return json_encode($this->error);
        }
        
        $guardianId = $param['patient_id'];
        $dir = PATH_REPORT . $param['hospital_id'] . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        
        $file = $dir . $guardianId . '.pdf';
        $ret = file_put_contents($file, $data);
        if (false === $ret) {
            $this->setError(6, 'IO error.');
            return json_encode($this->error);
        }
        
        $urlFile = 'report/' . $param['hospital_id'] . '/' . $guardianId . '.pdf';
        $ret = DbiAnalytics::getDbi()->uploadReport($guardianId, $urlFile);
        if (VALUE_DB_ERROR === $ret) {
            $this->setError(7, 'DB error.');
            return json_encode($this->error);
        }
        
        $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
        if (VALUE_DB_ERROR === $tree || array() == $tree) {
            //do nothing.
        } elseif ($tree['hospital_id'] != $tree['report_hospital']) {
            pushShortMessage($tree['hospital_id'], "病人(id=$guardianId)的报告已经上传到服务器，请下载打印。");
        } else {
            //do nothing.
        }
        
        return json_encode($this->retSuccess);
    }
    
    private function validate($param, $data)
    {
        if (!isset($param['hospital_id']) || trim($param['hospital_id']) == '') {
            $this->setError(1, 'hospital_id is empty.');
            return false;
        }
        if (!isset($param['patient_id']) || trim($param['patient_id']) == '') {
            $this->setError(2, 'patient_id is empty.');
            return false;
        }
        if (!isset($param['start_time']) || trim($param['start_time']) == '') {
            $this->setError(3, 'start_time is empty.');
            return false;
        }
        if (!isset($param['end_time']) || trim($param['end_time']) == '') {
            $this->setError(4, 'end_time is empty.');
            return false;
        }
        if (empty($data)) {
            $this->setError(5, 'no file uploaded.');
            return false;
        }
        return true;
    }
    
    private function setError($code, $message)
    {
        try {
            Logger::write($this->logFile, $message);
        } catch (Exception $e) {
            //do nothing.
        }
        $this->error['code'] = $code;
        $this->error['message'] = $message;
    }
}
