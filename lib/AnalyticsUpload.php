<?php
require_once PATH_CONFIG . 'value.php';
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'Dbi.php';

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
        
        $dir = PATH_REPORT . $param['patient_id'] . '\\';
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $file = $dir . $param['start_time'] . '_' . $param['end_time'] . '.pdf';
        $ret = file_put_contents($file, $data);
        if (false === $ret) {
            $this->setError(7, 'failed to save file.');
            return json_encode($this->error);
        }
        
        $ret = Dbi::getDbi()->updateHistoryReport($param['hospital_id'], 
                $param['patient_id'], $param['start_time'], $param['end_time']);
        if (VALUE_DB_ERROR === $ret) {
            $this->setError(7, 'failed to update db.');
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
        
        $existPatient = $this->checkPatient($param['hospital_id'], 
                $param['patient_id'], $param['start_time'], $param['end_time']);
        if ($existPatient == false) {
            $this->setError(6, 'patient not exists.');
            return false;
        }
        
        return true;
    }
    
    private function checkPatient($hospitalId, $patientId, $startTime, $endTime)
    {
        $table = 'guardian_history';
        $where = array(
                'hospital_id' => $hospitalId,
                'patient_id' => $patientId,
                'start_time' => $startTime,
                'end_time' => $endTime
        );
        return Dbi::getDbi()->existData($table, $where);
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
    
    private function updateHistory($id, $file, $alert)
    {
        $data = array(
                'pid' => $id,
                'recordTime' => date('YmdHis'),
                'alert' => $alert,
                'path' => str_replace(PATH_ROOT, '', $file)
        );
        return Dbi::getDbi()->insertEcg($data);
    }
    
//     $typeList = array('pdf');
//     function checkFileType($file, $typeList)
//     {
//         $file = trim($file);
//         if ($file == '') {
//             return false;
//         }
//         $extension = strtolower(substr(strrchr($file, '.'), 1));
//         foreach ($typeList as $type) {
//             if ($type != $extension) {
//                 return false;
//             }
//         }
//         return true;
//     }
}