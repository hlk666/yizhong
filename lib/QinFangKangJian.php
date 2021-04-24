<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'DbiHebei.php';

class QinFangKangJian
{
    private $logFile = 'qinfangkangjian_logic.log';
    private $logFileError = 'qinfangkangjian_error.log';
    private $baseUrl = 'https://qfkj.jiankanghebei.cn/devcore/service/';
    private $accessId = 'hUj4a70X6skS52wHFfrMeChgCOcyM88X';
    private $accessKey = '07dmDQXlQ3g7YoK6s0B8PweAxebz9QMR';
    private $noncestr = 'Y2i0Zh1o5ngYANY20iL2iTAI1ao';
    private $yizhongurl = 'http://101.200.174.235/hebei/';
    private $hebeiKey = 'hebeishengeryuan2021';
    public $VALUE_TIME = 'time';
    public $VALUE_KEY = 'key';
    public $VALUE_OK = 'ok';
    public $MESSAGE_AUTH = '认证未通过，请检查：';
    /*
    public function getDoctorList()
    {
        $ret = $this->request('queryDoctorList', ['section_id' => '725'], true);
        if ($ret === false) {
            return null;
        } else {
            return $ret['doctor_list'];
        }
    }
    */
    public function getReportFile($guardianId)
    {
        $file = md5($this->hebeiKey . $guardianId) . '.pdf';
        return $file;
    }
    public function checkBaseParam($timestamp, $key)
    {
        $currentTimestamp = $this->timestamp();
        if ($currentTimestamp - $timestamp < -30000 || $currentTimestamp - $timestamp > 30000) {
            return $this->VALUE_TIME;
        }
        $md5 = md5($this->hebeiKey . $timestamp);
        if ($md5 != $key) {
            return $this->VALUE_KEY;
        }
        return $this->VALUE_OK;
    }
    public function regist($guardianId)
    {
        $data = $this->getRegistInfo($guardianId);
        if (false === $data) {
            return false;
        }
        $ret = $this->request('dataPush', $data);
        if (false === $ret) {
            return false;
        }
        return $this->pushStatus($guardianId, '2');
    }
    public function upload($guardianId)
    {
        return $this->pushStatus($guardianId, '3');
    }
    public function report($guardianId, $yizhongDoctorId, $diagnosis)
    {
        $tree = DbiHebei::getDbi()->getHosDepDocTreeByDoctor($yizhongDoctorId);
        if ($tree === VALUE_DB_ERROR) {
            Logger::write($this->logFileError, MESSAGE_DB_ERROR . $hebeiDoctorId);
            return false;
        }
        if (empty($tree)) {
            Logger::write($this->logFileError, MESSAGE_DB_NO_DATA . $hebeiDoctorId);
            return false;
        }
        
        $data = ['diagnosis_id' => $guardianId, 'push_type' => '2', 'invited_doctor_list' => $tree];
        $data['diagnostic_advice'] = $diagnosis;
        $data['diagnostic_url'] = $this->yizhongurl . $this->getReportFile($guardianId);
        $data['nature'] = '1';
        $data['report_date'] = date('Y-m-d H:i:s');
        $ret = $this->request('dataPush', $data);
        if (false === $ret) {
            return false;
        }
        return $this->pushStatus($guardianId, '4');
    }
    
    private function pushStatus($guardianId, $status)
    {
        $data = ['diagnosis_id' => $guardianId, 'push_type' => '3', 'state'=> $status, 'date' => date('Y-m-d H:i:s')];
        return $this->request('dataPush', $data);
    }
    private function getTreeByYizhongId($hospitalId)
    {
        $tree = DbiHebei::getDbi()->getHosDepTree($hospitalId);
        if ($tree === VALUE_DB_ERROR) {
            Logger::write($this->logFileError, MESSAGE_DB_ERROR . $hospitalId);
            return false;
        }
        if (empty($tree)) {
            Logger::write($this->logFileError, MESSAGE_DB_NO_DATA . $hospitalId);
            return false;
        }
        return $tree;
    }
    private function getDoctorTreeByYizhongId($yizhongHospital)
    {
        $tree = DbiHebei::getDbi()->getHosDepDocTree($yizhongHospital);
        if ($tree === VALUE_DB_ERROR) {
            Logger::write($this->logFileError, MESSAGE_DB_ERROR . $hospitalId);
            return false;
        }
        if (empty($tree)) {
            Logger::write($this->logFileError, MESSAGE_DB_NO_DATA . $hospitalId);
            return false;
        }
        return $tree;
    }
    public function getRegistInfo($guardianId)
    {
        $patient = DbiHebei::getDbi()->getPatientInfo($guardianId);
        if ($patient === VALUE_DB_ERROR) {
            Logger::write($this->logFileError, MESSAGE_DB_ERROR . $guardianId);
            return false;
        }
        if (empty($patient)) {
            Logger::write($this->logFileError, MESSAGE_DB_NO_DATA . $guardianId);
            return false;
        }
        $applyTree = $this->getTreeByYizhongId($patient['regist_hospital_id']);
        if (false === $applyTree || empty($applyTree)) {
            return false;
        }
        $analyticsTree = $this->getDoctorTreeByYizhongId($patient['moved_hospital']);
        if (false === $analyticsTree || empty($analyticsTree)) {
            return false;
        }
        $data = ['diagnosis_id' => $guardianId, 'push_type' => '1'];
        $data['apply_hospital_name'] = $applyTree['hospital_name'];
        $data['apply_hospital_id'] = $applyTree['hospital_id'];
        $data['apply_section_name'] = $applyTree['department_name'];
        $data['apply_section_id'] = $applyTree['department_id'];
        
        $data['apply_doctor_name'] = $patient['doctor_name'];
        $data['apply_doctor_id'] = $patient['doctor_id'];
        $data['patient_name'] = $patient['patient_name'];
        $data['patient_age'] = date('Y') - $patient['birth_year'];
        $data['patient_gender'] = $patient['sex'];
        $data['patient_idcard'] = $patient['id_card'];
        $data['patient_telephone'] = $patient['tel'];
        $data['patient_mi_card'] = $patient['mi_card'];
        $data['disease_id'] = $patient['disease_id'];
        $data['disease_name'] = $patient['disease_name'];
        $data['inspection_type'] = '14';
        $data['inspect_position'] = '心脏';
        $data['inspect_section_name'] = $applyTree['department_name'];
        $data['inspect_section_id'] = $applyTree['department_id'];
        $data['inspection_doctor_name'] = $patient['doctor_name'];
        $data['inspection_doctor_id'] = $patient['doctor_id'];
        $data['inspect_findings'] = $patient['inspect_findings'];
        
        $data['inspect_date'] = $patient['start_time'];
        $data['apply_date'] = $patient['start_time'];
        $data['invited_doctor_list'] = $analyticsTree;
        
        return $data;
    }
    
    private function timestamp()
    {
        list($usec, $sec) = explode(' ', microtime());
        $time = ($sec . substr($usec, 2, 3));
        return $time;
    }
    private function getBaseParam()
    {
        $tmp = 'accessid=' . $this->accessId . '&noncestr=' . $this->noncestr . '&timestamp=' . $this->timestamp();
        $tmpWighKey = $this->accessKey . $tmp . $this->accessKey;
        $sign = md5($tmpWighKey);
        return $tmp . '&sign=' . $sign;
    }
    private function request($entry, $data, $requireResult = false)
    {
        $url = $this->baseUrl . $entry . '?' . $this->getBaseParam();
        $headers = array(
                        "Content-type: application/json;charset='utf-8'",
                        "Accept: application/json",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache"
        );
        $post = json_encode($data, JSON_UNESCAPED_UNICODE);
        Logger::write($this->logFile, $entry . ':' . $post);
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $ret = curl_exec($ch);
        curl_close($ch);
        
        $retArray = json_decode($ret, true);
        Logger::write($this->logFile, $ret);
        if (isset($retArray['code']) && $retArray['code'] == '0') {
            Logger::write($this->logFile, 'succeed');
            if ($requireResult) {
                return $retArray['resultObjects'];
            } else {
                return true;
            }
        } else {
            Logger::write($this->logFile, $ret);
            return false;
        }*/return true;
    }
}
