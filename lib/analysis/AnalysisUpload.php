<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_ROOT . 'lib/DbiAnalytics.php';

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
        
        //to fix bug of client. start.
        $dir1 = PATH_ROOT . $type . DIRECTORY_SEPARATOR;
        if (!file_exists($dir1)) {
            mkdir($dir1);
        }
        
        if ('hbi' == $type) {
            $file1 = $dir1 . $guardianId . '.hbi';
        }
        if ('report' == $type) {
            $file1 = $dir1 . $guardianId . '.pdf';
        }
        
        $ret = file_put_contents($file1, $data);
        if (false === $ret) {
            $this->setError(5, 'IO error.');
            return json_encode($this->error);
        }
        //end
        
        $hbiDoctor = isset($param['hbi_doctor']) ? $param['hbi_doctor'] : '0';
        $reportDoctor = isset($param['report_doctor']) ? $param['report_doctor'] : '0';
        //need check user and password here.
        $ret = DbiAnalytics::getDbi()->setDataStatus($guardianId, $type, $hbiDoctor, $reportDoctor);
        if (VALUE_DB_ERROR === $ret) {
            api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
        }
        
        $message = isset($param['message']) ? $param['message'] : '0';
        if ('1' == $message) {
            $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
            if (VALUE_DB_ERROR !== $tree && array() !== $tree) {
                if ('hbi' == $type) {
                    setNotice($tree['report_hospital'], $type, $guardianId);
                }
                if ('report' == $type && $tree['hospital_id'] != $tree['report_hospital']) {
                    setNotice($tree['hospital_id'], $type, $guardianId);
                }
            }
            
            $hospitalTo = DbiAnalytics::getDbi()->getHospitalByPatient($guardianId);
            if (VALUE_DB_ERROR === $hospitalTo) {
                api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
            }
            $hospitalFrom = DbiAnalytics::getDbi()->getHospitalFrom($guardianId, $hospitalTo);
            if (VALUE_DB_ERROR !== $hospitalFrom && '' !== $hospitalFrom) {
                if ('hbi' == $type) {
                    $ret = DbiAnalytics::getDbi()->moveData($guardianId, $hospitalTo, $hospitalFrom, '1');
                    if (VALUE_DB_ERROR === $ret) {
                        api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
                    }
                    //setNotice($hospitalFrom, 'hbi', $guardianId);
                    $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
                    if (VALUE_DB_ERROR !== $tree && array() !== $tree) {
                        setNotice($tree['report_hospital'], 'hbi', $guardianId);
                    }
                    else {
                        setNotice($hospitalFrom, 'hbi', $guardianId);
                    }
                }
                if ('report' == $type) {
                    //not exist.
                }
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
