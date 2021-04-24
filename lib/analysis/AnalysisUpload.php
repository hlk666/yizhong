<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_ROOT . 'lib/DbiAnalytics.php';
require_once PATH_ROOT . 'lib/Dbi.php';
//require_once PATH_ROOT . 'lib/DbiChronic.php';
require_once PATH_LIB . 'ShortMessageService.php';
require_once PATH_LIB . 'Mqtt.php';
require_once PATH_LIB . 'QinFangKangJian.php';

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
        $isHebei = isset($param['is_hebei']) ? '1' : '0';
        
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
        
        $dbPatient = DbiAnalytics::getDbi()->getPatientWhenUploadReport($guardianId);
        if (VALUE_DB_ERROR === $dbPatient || empty($dbPatient)) {
            //do nothing.
        } else {
            //for anzhong.
            if ($tree['report_hospital'] == '872') {
                $dbPatient['diagnosis'] = isset($param['diagnosis']) ? $param['diagnosis'] : '';
            }
            setPatient($guardianId, $dbPatient);
        }
        
        //for anzhong start
        if (Dbi::getDbi()->isAnzhongChild($tree['hospital_id'])) {
            include_once PATH_LIB . 'AnZhong.php';
            $isSuccess = AnZhong::report($guardianId);
            if (!$isSuccess) {
                ShortMessageService::send('13465596133', '安徽中医院上传报告，但是调用接口失败，病人id：' . $guardianId);
            }
        }
        //for anzhong end
        //special action for hebei start.
        if ($isHebei == '1') {
            $obj = new QinFangKangJian();
            
            $retFile = rename($file, PATH_ROOT . 'hebei' . DIRECTORY_SEPARATOR . $obj->getReportFile($guardianId));
            if (false === $retFile) {
                ShortMessageService::send('13465596133', '河北省二院出报告，但是文件重命名失败，病人id：' . $guardianId);
            }
            $diagnosis = isset($param['diagnosis']) ? $param['diagnosis'] : '';
            $retHebei = $obj->report($guardianId, $param['report_doctor'], $diagnosis);
            if ($retHebei === false) {
                ShortMessageService::send('13465596133', '河北省二院出报告，但是调用接口失败，病人id：' . $guardianId);
            } else {
                ShortMessageService::send('13465596133', '河北省二院出报告，传输数据成功，病人id：' . $guardianId);
            }
        }
        //special action for hebei end.
        
        if ($type == 'hbi' && $tree['analysis_hospital'] != $tree['report_hospital']) {
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
