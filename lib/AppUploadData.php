<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';

class AppUploadData
{
    private $error = array();
    private $logFile = 'upload.log';
    private $retSuccess = array('code' => 0, 'message' => '');
    
    public function run($patientId, $mode, $alert = 0, $time = '', $data = '', $size = 0)
    {
        $ret = $this->validate($patientId, $mode, $time, $data, $size);
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
            //$retIO = file_put_contents($file, $data);
            $retIO = file_put_contents($file, gzdecode($data));
            if ($retIO === false) {
                $this->setIOError();
                Logger::write($this->logFile, 'failed to save file on ID of :' . $patientId);
                return json_encode($this->error);
            }
            if (filesize($file) == 120570) {
                $fh = fopen($file, "rb");
                $head = fread($fh, filesize($file));
                fclose($fh);
                
                $arr = unpack("C*", $head);
                $length = count($arr)/4019;
                $index = ($length - 1) * 4019 + 1;
                $phonePower = '-1';
                $collectionPower = '-1';
                $line = '-1';
                $bluetooth = '-1';
                for ($i = $index; $i < $index + 19; $i++) {
                    if ($i - $index == 6) {
                        $phonePower = $arr[$i];
                    }
                    if ($i - $index == 7) {
                        $collectionPower = $arr[$i];
                    }
                    if ($i - $index == 17) {
                        $line = ($arr[$i] == 1 ? '0' : '1');
                    }
                }
                
                for ($i = 0; $i < $length; $i++) {
                    for ($j = $i * 4019 + 20; $j < $i * 4019 + 4020; $j++) {
                        $temp .= pack('C*', $arr[$j]);
                    }
                }
                file_put_contents($file, $temp);
                
                $deviceId = Dbi::getDbi()->getDeviceId($patientId);
                if ($deviceId !== VALUE_DB_ERROR && !empty($deviceId)) {
                    $ret = Dbi::getDbi()->addDeviceStatus($deviceId, $phonePower, $collectionPower, $bluetooth, $line);
                    if (VALUE_DB_ERROR === $ret) {
                        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
                    }
                    
                    $file = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'device_status' . DIRECTORY_SEPARATOR . $deviceId . '.php';
                    
                    $template = "<?php\n";
                    $template .= '$phone_power = \'' . $phonePower . "';\n";
                    $template .= '$collection_power = \'' . $collectionPower . "';\n";
                    $template .= '$bluetooth = \'' . $bluetooth . "';\n";
                    $template .= '$line = \'' . $line . "';\n";
                    $template .= '$time = \'' . date('Y-m-d H:i:s') . "';\n";
                    
                    $handle = fopen($file, 'w');
                    fwrite($handle, $template);
                    fclose($handle);
                }
            }
            
            $hospital = Dbi::getDbi()->getGuardianHospital($patientId);
            if (VALUE_DB_ERROR === $hospital) {
                $this->setError(5, 'Server DB error.');
                return json_encode($this->error);
            }
            
            $urlFile = 'ECG/' . $patientId . '/' . $time . '.bin';
            $retDB = Dbi::getDbi()->flowGuardianAddEcg($patientId, $alert, $time, $urlFile);
            if (VALUE_DB_ERROR === $retDB) {
                Logger::write($this->logFile, 'failed to save db data on ID of :' . $patientId);
                $this->setError(5, 'Server DB error.');
                return json_encode($this->error);
            }
            
            if (!empty($hospital)) {
                $cacheEcgDataFile = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'ecg_data' . DIRECTORY_SEPARATOR . $hospital . '.txt';
                $cacheEcgData = file_get_contents($cacheEcgDataFile) . $patientId . ',' . $retDB . ',' . $alert . ',' . $time . ',' . $urlFile . ';';
                file_put_contents($cacheEcgDataFile, $cacheEcgData);
                
                $fileShift = PATH_DATA . 'shift.txt';
                $userList = explode(';', file_get_contents($fileShift));
                foreach ($userList as $user) {
                    if (empty($user)) {
                        continue;
                    }
                    $tmp = explode(',', $user);
                    if (isset($tmp[0]) && !empty($tmp[0])) {
                        $cacheEcgDataFileAll = PATH_ROOT . 'cache' . DIRECTORY_SEPARATOR . 'ecg_data' . DIRECTORY_SEPARATOR . '1_' . $tmp[0] . '.txt';
                        $cacheEcgDataAll = file_get_contents($cacheEcgDataFileAll) . $patientId . ',' . $retDB . ',' . $alert . ',' . $time . ',' . $urlFile . ';';
                        file_put_contents($cacheEcgDataFileAll, $cacheEcgDataAll);
                    }
                }
            }
            
            $hospitalInfo = Dbi::getDbi()->getHospitalByGuardian($patientId);
            if (VALUE_DB_ERROR !== $hospitalInfo) {
                setNotice($hospitalInfo['guard_hospital_id'], 'ecg_notice', $patientId);
            }
        }
        return json_encode($this->retSuccess);
    }
    
    private function validate($patientId, $mode, $time, $data, $size)
    {
        if (!isset($patientId) || trim($patientId) == '' || empty($patientId)) {
            $this->setError(1, 'Patient id is required.');
            return false;
        }
        
        if (!isset($mode) || trim($mode) == '' || !in_array($mode, [1, 2, 3])) {
            $this->setError(2, $patientId . ' : Mode is empty or wrong.');
            return false;
        }
        
        if (!isset($time) || trim($time) == '') {
            $this->setError(3, $patientId . ' : Alert time is required.');
            return false;
        }
        
        if (!isset($data) || trim($data) == '') {
            $this->setError(4, $patientId . ' : Detail data error.');
            return false;
        }
        
        $len = strlen($data);
        if ($size == 0) {
            if ($len != 20000 && $len != 120000 && $len != 80000) {
                Logger::write($this->logFile, $patientId . ' : length of data : ' . $len);
            }
            if ($len % 4000 > 0) {
                $this->setError(5, $patientId . ' : Data size is wrong.');
                return false;
            }
        } else {
            if ($len != $size) {
                $this->setError(5, $patientId . ' : length is ' . $len . ', size is ' . $size);
                return false;
            }
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