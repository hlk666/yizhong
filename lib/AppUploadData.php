<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';

class AppUploadData
{
    private $error = array();
    private $logFile = 'uploadDataLog.txt';
    private $retSuccess = array('code' => 0, 'message' => '');
    
    public function run($post)
    {
        $data = $this->validate($post);
        if ($data === false) {
            return json_encode($this->error);
        }
    
        $id = $data['patient_id'];
        $mode = $data['mode'];
        $alert = isset($data['alert']) ? $data['alert'] : 0;
        $ecgData = $data['data'];

        if ($mode == 1) {
            $realTimeDir = PATH_REAL_TIME . $id . '\\';
            if (!is_dir($realTimeDir)) {
                mkdir($realTimeDir);
            }
            $tmpFile = $realTimeDir . date('YmdHis') . '.tmp';
            $retIO = $this->writeFile($tmpFile, $ecgData);
            if ($retIO === false) {
                $this->setIOError();
                return json_encode($this->error);
            }
            
            $realTimeFile = $realTimeDir . $id . SUFFIX_REAL_TIME_FILE;
            $retIO = rename($tmpFile, $realTimeFile);
            if ($retIO === false) {
                $this->setIOError();
                return json_encode($this->error);
            }
        }
        
        if ($mode == 2 || $mode == 3) {
            $file = $this->getEcgFile($id);
            $retIO = $this->writeFile($file, $ecgData);
            if ($retIO === false) {
                $this->setIOError();
                return json_encode($this->error);
            }
            
            $retDB = $this->insertData($id, $file, $alert);
            if ($retDB <= 0) {
                $this->setError(7, 'Server DB error.');
                return json_encode($this->error);
            }
        }
        return json_encode($this->retSuccess);
    }
    
    private function validate($input)
    {
        if (!isset($input['DATA'])) {
            $this->setError(1, 'Post data is empty.');
            return false;
        }
        
        $post = trim($input['DATA']);
        if (empty($post)) {
            $this->setError(1, 'Post data is empty.');
            return false;
        }
        
        $data = json_decode($post, true);
        if ($data === false || $data === null) {
            $this->setError(2, 'Json formatter error.');
            return false;
        }
        
        if (!isset($data['patient_id']) || trim($data['patient_id']) == '') {
            $this->setError(3, 'Patient id is required.');
            return false;
        }
        
        if (!isset($data['mode']) || trim($data['mode']) == '' || !in_array($data['mode'], [1, 2, 3])) {
            $this->setError(4, 'Mode is empty or wrong.');
            return false;
        }
        
        if (!isset($data['data']) || trim($data['data']) == '') {
            $this->setError(5, 'Detail data is empty.');
            return false;
        }
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