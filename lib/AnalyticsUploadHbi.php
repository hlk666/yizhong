<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'DbiAnalytics.php';

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
        
        $dir = PATH_HBI . $param['hospital_id'] . DIRECTORY_SEPARATOR;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $file = $dir . $param['patient_id'] . '.hbi';
        $ret = file_put_contents($file, $data);
        if (false === $ret) {
            $this->setError(4, 'IO error.');
            return json_encode($this->error);
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
