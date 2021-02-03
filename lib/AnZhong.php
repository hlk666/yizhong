<?php
require_once PATH_LIB . 'Logger.php';

class AnZhong
{
    //private static $url = 'https://apictr.health.dev.zoenet.cn/ahzyyEcgCtrl/';
    private static $url = 'https://apictr.health.zoenet.cn/ahzyyEcgCtrl/';
    private static $urlYizhong = 'http://101.200.174.235/report/';
    private static $log = 'anzhong.log';
    
    public static function regist($guardianId)
    {
        $data = array();
        $patient = getPatient($guardianId);
        if (empty($patient)) {
            Logger::write(self::$log, "patient of $guardianId is empty.");
            return false;
        }
        
        $data['id'] = $guardianId;
        $data['hospitalName'] = $patient['regist_hospital_name'];
        $data['patientName'] = $patient['patient_name'];
        $data['patientTel'] = $patient['patient_tel'];
        $data['patientAge'] = $patient['age'];
        $data['patientSex'] = $patient['sex'];
        $data['registTime'] = $patient['start_time'];
        $data['doctorName'] = $patient['regist_doctor_name'];
        
        return self::request('receiveEcgData', $data);
    }
    
    public static function upload($guardianId)
    {
        $data = array();
        $patient = getPatient($guardianId);
        //Logger::write(self::$log, $guardianId);
        //Logger::write(self::$log, var_export($patient, true));exit;
        if (empty($patient)) {
            Logger::write(self::$log, "patient of $guardianId is empty.");
            return false;
        }
        
        $data['id'] = $guardianId;
        $data['startTime'] = $patient['start_time'];
        $data['endTime'] = !isset($patient['end_time']) || empty($patient['end_time']) ? $patient['upload_time'] : $patient['end_time'];
        $data['uploadTime'] = $patient['upload_time'];
        
        return self::request('uploadEcgData', $data);
    }
    
    public static function report($guardianId)
    {
        $data = array();
        $patient = getPatient($guardianId);
        if (empty($patient)) {
            Logger::write(self::$log, "patient of $guardianId is empty.");
            return false;
        }
        
        $data['id'] = $guardianId;
        $data['interpretationDoctorName'] = $patient['report_doctor_name'];
        $data['pdfUrl'] = self::$urlYizhong . $guardianId . '.pdf';
        $data['diagnosis'] = $patient['diagnosis'];
        
        return self::request('receiveEcgResult', $data);
    }
    
    public static function request($entry, $data)
    {
        $url = self::$url . $entry;
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );
        $post = json_encode($data, JSON_UNESCAPED_UNICODE);
        Logger::write(self::$log, $entry . ':' . $post);
        
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
        if (isset($retArray['code']) && $retArray['code'] == '0') {
            Logger::write(self::$log, 'succeed');
            return true;
        } else {
            Logger::write(self::$log, $ret);
            return false;
        }
    }
}
