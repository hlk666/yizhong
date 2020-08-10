<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_ROOT . 'lib/DbiAnalytics.php';
//require_once PATH_ROOT . 'lib/DbiChronic.php';
require_once PATH_LIB . 'ShortMessageService.php';
require PATH_LIB . 'Mqtt.php';

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
        
        $ret = $this->validate($param, $data, $type);
        if (false === $ret) {
            return json_encode($this->error);
        }
        
        $guardianId = $param['patient_id'];
        
        $oldReportDoctor = DbiAnalytics::getDbi()->getReportDoctor($guardianId);
        if (VALUE_DB_ERROR === $oldReportDoctor) {
            api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
        }
        if ($type == 'report' 
                && $oldReportDoctor != '0' 
                && $oldReportDoctor != '' 
                && $oldReportDoctor != $param['report_doctor']) {
                    api_exit(['code' => '2', 'message' => '只有本人才能修改已经出过的报告。']);
        }
        
        $dir = PATH_ROOT . $type . DIRECTORY_SEPARATOR;
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
        
        /*
        $url = URL_ROOT . 'report/' . $guardianId . '.pdf';
        $guardianForChronic = DbiAnalytics::getDbi()->getGuardianForChronic($guardianId);
        if (VALUE_DB_ERROR === $guardianForChronic || empty($guardianForChronic) || empty($guardianForChronic['patient_id'])) {
            //do nothing because it is not importment for analytics.
        } else {
            DbiChronic::getDbi()->addEcgExamination($guardianForChronic['patient_id'], $guardianId, $url, $guardianForChronic['result']);
        }
        */
        $message = isset($param['message']) ? $param['message'] : '0';
        $hbiDoctor = isset($param['hbi_doctor']) ? $param['hbi_doctor'] : '0';
        $reportDoctor = isset($param['report_doctor']) ? $param['report_doctor'] : '0';
        $tutorDoctor = isset($param['tutor_doctor']) ? $param['tutor_doctor'] : '0';
        $identity = isset($param['identity']) ? $param['identity'] : '2';  //only used in hbi case. '2' means common doctor.
        
        //message is 
        //0:only save file.
        //1:save file, update db, send message.
        //2:save file, update db.
        if ($message == '1' || $message == '2') {
            $ret = DbiAnalytics::getDbi()->setDataStatus($guardianId, $type, $hbiDoctor, $reportDoctor, $tutorDoctor, $identity);
            if (VALUE_DB_ERROR === $ret) {
                api_exit(['code' => '2', 'message' => MESSAGE_DB_ERROR]);
            }
        }
        
        $tree = DbiAnalytics::getDbi()->getHospitalTree($guardianId);
        if (VALUE_DB_ERROR === $tree || empty($tree)) {
            return json_encode($this->retSuccess);
        }
        if ('1' == $message) {
            if ('hbi' == $type) {
                setNotice($tree['report_hospital'], $type, $guardianId);
                if ($tree['report_hospital'] == 175) {
                    ShortMessageService::send('13963896768', '有新的已分析数据，请审阅报告。');
                }
            }
            if ('report' == $type && $tree['hospital_id'] != $tree['report_hospital']) {
                setNotice($tree['hospital_id'], $type, $guardianId);
            }
        }
        
        if ($type == 'hbi' && $tree['analysis_hospital'] != $tree['report_hospital']) {
            $dbPatient = DbiAnalytics::getDbi()->getPatientWhenUploadReport($guardianId);
            if (VALUE_DB_ERROR === $dbPatient || empty($dbPatient)) {
                //do nothing.
            } else {
                setPatient($guardianId, $dbPatient);
            }
            $mqttMessage = 'patient_id=' . $guardianId
                . ',data_status=' . $dbPatient['data_status']
                . ',report_time=' . $dbPatient['report_time']
                . ',hbi_doctor=' . $dbPatient['hbi_doctor']
                . ',hbi_doctor_name=' . $dbPatient['hbi_doctor_name']
                . ',report_doctor=' . $dbPatient['report_doctor']
                . ',report_doctor_name=' . $dbPatient['report_doctor_name'];
            $mqtt = new Mqtt();
            $data = [['type' => 'holter', 'id' => $tree['report_hospital'], 'event'=>'upload_hbi', 'message'=>$mqttMessage]];
            $mqtt->publish($data);
        }
        if ($type == 'report' && $tree['hospital_id'] != $tree['report_hospital']) {
            $dbPatient = DbiAnalytics::getDbi()->getPatientWhenUploadReport($guardianId);
            if (VALUE_DB_ERROR === $dbPatient || empty($dbPatient)) {
                //do nothing.
            } else {
                setPatient($guardianId, $dbPatient);
            }
            $mqttMessage = 'patient_id=' . $guardianId
                . ',data_status=' . $dbPatient['data_status']
                . ',report_time=' . $dbPatient['report_time']
                . ',hbi_doctor=' . $dbPatient['hbi_doctor']
                . ',hbi_doctor_name=' . $dbPatient['hbi_doctor_name']
                . ',report_doctor=' . $dbPatient['report_doctor']
                . ',report_doctor_name=' . $dbPatient['report_doctor_name'];
            $mqtt = new Mqtt();
            $data = [['type' => 'online', 'id' => $tree['hospital_id'], 'event'=>'upload_report', 'message'=>$mqttMessage]];
            $mqtt->publish($data);
        }
        
        if ($type == 'report') {
            clearNotice($tree['analysis_hospital'], 'upload_data', $guardianId);
            clearNotice($tree['report_hospital'], 'upload_data', $guardianId);
            
            //special action for zhongda start.
            /*
            if (in_array($tree['report_hospital'], [132])) {
                $ret = DbiAnalytics::getDbi()->setZhongdaData($guardianId);
                if (VALUE_DB_ERROR === $ret) {
                    Logger::write('zhongda.log', "zhongda error.");
                }
                $file = PATH_ROOT . 'zhongda_report' . DIRECTORY_SEPARATOR . $guardianId . '.pdf';
                $ret = file_put_contents($file, $data);
                if (false === $ret) {
                    Logger::write('zhongda.log', "faied to save file to zhongda dir.");
                }
            }
            */
            //special action for zhongda end.
        }
        
        return json_encode($this->retSuccess);
    }
    
    private function validate($param, $data, $type)
    {
        if (!isset($param['patient_id']) || trim($param['patient_id']) == '') {
            $this->setError(1, 'patient_id is empty.');
            return false;
        }
        if (empty($data)) {
            $this->setError(1, 'no file uploaded.');
            return false;
        }
        
        if ($type == 'report') {
            $len = strlen($data);
            $size = isset($param['size']) ? $param['size'] : 0;
            Logger::write($this->logFile, $param['patient_id'] . ' : ' . $size);
            if ($size != 0 && $len != $size) {
                $this->setError(2, $param['patient_id'] . ' : Data size is wrong.');
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
}
