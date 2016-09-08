<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_ROOT . 'lib/DbiAnalytics.php';
require_once PATH_ROOT . 'lib/tool/HpMessage.php';

class AnalysisUpload
{
    private $error = array();
    private $logFile = 'analysisLog.txt';
    private $retSuccess = array('code' => 0, 'message' => MESSAGE_SUCCESS);
    
    public function run($param, $data, $type)
    {
        if (empty($type)) {
            $this->setError(99, MESSAGE_OTHER_ERROR);
            return json_encode($this->error);
        }
        
        $ret = $this->validate($param, $data);
        if (false === $ret) {
            return json_encode($this->error);
        }
        
        $guardianId = $param['patient_id'];
        $dir = PATH_ROOT . $type . DIRECTORY_SEPARATOR . $param['hospital_id'] . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        
        if ('hbi' == $type) {
            $file = $dir . $guardianId . '.hbi';
        }
        if ('report' == $type) {
            $file = $dir . $guardianId . '.pdf';
        }
        
        $ret = file_put_contents($file, $data);
        if (false === $ret) {
            $this->setError(5, 'IO error.');
            return json_encode($this->error);
        }
        
        if ('hbi' == $type) {
            $message = isset($param['message']) ? $param['message'] : '0';
            if ('1' == $message) {
                $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
                if (VALUE_DB_ERROR !== $tree && array() !== $tree) {
                    HpMessage::sendTelMessage("病人(id=$guardianId)的心搏数据文件已经分析完毕，请出报告。", $tree['report_hospital']);
                }
            }
        }
        if ('report' == $type) {
            $urlFile = 'report/' . $param['hospital_id'] . '/' . $guardianId . '.pdf';
            $ret = DbiAnalytics::getDbi()->uploadReport($guardianId, $urlFile);
            if (VALUE_DB_ERROR === $ret) {
                $this->setError(2, MESSAGE_DB_ERROR);
                return json_encode($this->error);
            }
            
            $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
            if (VALUE_DB_ERROR !== $tree && array() !== $tree && $tree['hospital_id'] != $tree['report_hospital']) {
                HpMessage::sendTelMessage("病人(id=$guardianId)的报告已经上传到服务器，请下载打印。", $tree['hospital_id']);
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
            $this->setError(1, 'patient_id is empty.');
            return false;
        }
        if (empty($data)) {
            $this->setError(1, 'no file uploaded.');
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
