<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'DbiAnalytics.php';
require_once PATH_LIB . 'ShortMessageService.php';

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

class AnalyticsUploadHbi
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
        $dir = PATH_HBI . $param['hospital_id'] . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $file = $dir . $guardianId . '.hbi';
        $ret = file_put_contents($file, $data);
        if (false === $ret) {
            $this->setError(4, 'IO error.');
            return json_encode($this->error);
        }
        
        $message = isset($param['message']) ? $param['message'] : '0';
        if ($message == '1') {
            $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
            if (VALUE_DB_ERROR === $tree || array() == $tree) {
                //do nothing.
            } else {
                pushShortMessage($tree['report_hospital'], "病人(id=$guardianId)的心搏数据文件已经分析完毕，请出报告。");
            }
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
        if (empty($data)) {
            $this->setError(3, 'no file uploaded.');
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
