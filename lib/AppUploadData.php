<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';

class AppUploadData
{
    private $error = array();
    private $logFile = 'uploadDataLog.txt';
    private $retSuccess = array('code' => 0, 'message' => '');
    
    public function run($patientId, $mode, $alert = 0, $time = '', $data = array())
    {
        $ret = $this->validate($patientId, $mode, $time, $data);
        if ($ret === false) {
            return json_encode($this->error);
        }

        if ($mode == 1) {
            $realTimeDir = PATH_REAL_TIME . $patientId . DIRECTORY_SEPARATOR;
            if (!is_dir($realTimeDir)) {
                mkdir($realTimeDir);
            }
            $tmpFile = $realTimeDir . date('YmdHis') . '.tmp';
            $retIO = file_put_contents($tmpFile, $data);
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
            $dir = PATH_ECG . $patientId . DIRECTORY_SEPARATOR;
            if (!is_dir($dir)) {
                mkdir($dir);
            }
            $file = $dir . $time . '.bin';
            $retIO = file_put_contents($file, $data);
            if ($retIO === false) {
                $this->setIOError();
                Logger::write($this->logFile, 'failed to save file on ID of :' . $patientId);
                return json_encode($this->error);
            }
            
            $urlFile = 'ECG/' . $patientId . '/' . $time . '.bin';
            $retDB = Dbi::getDbi()->flowGuardianAddEcg($patientId, $alert, $time, $urlFile);
            if (VALUE_DB_ERROR === $retDB) {
                Logger::write($this->logFile, 'failed to save db data on ID of :' . $patientId);
                $this->setError(5, 'Server DB error.');
                return json_encode($this->error);
            }
            $this->setEcgNotice($patientId);
        }
        return json_encode($this->retSuccess);
    }
    
    private function setEcgNotice($guardianId)
    {
        $hospital = Dbi::getDbi()->getHospitalByGuardian($guardianId);
        if (VALUE_DB_ERROR === $hospital) {
            return;
        }
        $file = PATH_CACHE_ECG_NOTICE . $hospital['guard_hospital_id'] . '.php';
        if (file_exists($file)) {
            include $file;
            $patients[] = $guardianId;
            $patients = array_unique($patients);
        } else {
            $patients = array();
            $patients[] = $guardianId;
        }
        $template = "<?php\n";
        $template .= '$patients = array();' . "\n";
        
        foreach ($patients as $patient) {
            $template .= "\$patients[] = '$patient';\n";
        }
        $template .= "\n";
        
        $handle = fopen($file, 'w');
        fwrite($handle, $template);
        fclose($handle);
    }
    
    private function validate($patientId, $mode, $time, $data)
    {
        if (!isset($patientId) || trim($patientId) == '') {
            $this->setError(1, 'Patient id is required.');
            return false;
        }
        
        if (!isset($mode) || trim($mode) == '' || !in_array($mode, [1, 2, 3])) {
            $this->setError(2, 'Mode is empty or wrong.');
            return false;
        }
        
        if (!isset($time) || trim($time) == '') {
            $this->setError(3, 'alert time is required.');
            return false;
        }
        
        if (!isset($data) || trim($data) == '') {
            $this->setError(4, 'Detail data error.');
            return false;
        }
        
        $len = strlen($data);
        if ($len != 120000 && $len != 80000) {
            Logger::write($this->logFile, 'length of data : ' . $len);
        }
        if ($len % 4000 > 0) {
            $this->setError(5, 'Data size is wrong.');
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
    
    private function setIOError()
    {
        $this->setError(4, 'Server IO error.');
    }
}