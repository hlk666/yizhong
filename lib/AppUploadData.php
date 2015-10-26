<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';

class AppUploadData
{
    private $error = array();
    private $logFile = 'uploadDataLog.txt';
    private $retSuccess = array('code' => 0, 'message' => '');
    
    public function run($patientId, $mode, $alert = 0, $data = array())
    {
        $ret = $this->validate($patientId, $mode, $data);
        if ($ret === false) {
            return json_encode($this->error);
        }

        if ($mode == 1) {
            $realTimeDir = PATH_REAL_TIME . $patientId . '\\';
            if (!is_dir($realTimeDir)) {
                mkdir($realTimeDir);
            }
            $tmpFile = $realTimeDir . date('YmdHis') . '.tmp';
            $retIO = $this->writeFile($tmpFile, $data);
            if ($retIO === false) {
                $this->setIOError();
                return json_encode($this->error);
            }
            
            $realTimeFile = $realTimeDir . $patientId . SUFFIX_REAL_TIME_FILE;
            $retIO = rename($tmpFile, $realTimeFile);
            if ($retIO === false) {
                $this->setIOError();
                return json_encode($this->error);
            }
        }
        
        if ($mode == 2 || $mode == 3) {
            $file = $this->getEcgFile($patientId);
            $retIO = $this->writeFile($file, $data);
            if ($retIO === false) {
                $this->setIOError();
                return json_encode($this->error);
            }
            
            $retDB = $this->insertData($patientId, $file, $alert);
            if ($retDB <= 0) {
                $this->setError(7, 'Server DB error.');
                return json_encode($this->error);
            }
        }
        return json_encode($this->retSuccess);
    }
    
    private function validate($patientId, $mode, $data)
    {
        if (!isset($patientId) || trim($patientId) == '') {
            $this->setError(3, 'Patient id is required.');
            return false;
        }
        
        if (!isset($mode) || trim($mode) == '' || !in_array($mode, [1, 2, 3])) {
            $this->setError(4, 'Mode is empty or wrong.');
            return false;
        }
        
        if (!isset($data) || trim($data) == '') {
            $this->setError(5, 'Detail data error.');
            return false;
        }
        
        //@todo check length of $data here.
        Logger::write($this->logFile, '\r\nlength of data : ' . strlen($data) . '\r\n');
        return $data;
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
    
    private function getEcgFile($id)
    {
        $dir = PATH_ECG . $id . '\\';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir . date('Y-m-d H_i_s') . '.txt';
    }
    
    private function writeFile($file, $data)
    {
        try {
            $handle = fopen($file, 'a');
            if ($handle == false) {
                return false;
            }
            fwrite($handle, $data);
            fclose($handle);
        } catch (Exception $e) {
            Logger::write($this->logFile, $e->getMessage());
        }
        
        return true;
    }
    
    private function insertData($id, $file, $alert)
    {
        $data = array(
                'pid' => $id,
                'recordTime' => date('YmdHis'),
                'alert' => $alert,
                'path' => str_replace(PATH_ROOT, '', $file)
        );
        return Dbi::getDbi()->insertEcg($data);
    }
    
    private function setIOError()
    {
        $this->setError(6, 'Server IO error.');
    }
}