<?php
require_once PATH_ROOT . 'config/diagnosis.php';
require_once PATH_ROOT . 'lib/DbiAnalytics.php';

class HpPatientDiagnosis
{
    public static function addPatientDiagnosis($patientId, $diagnosis, $separator = "\r\n")
    {
        if (empty($patientId))
        {
            return ['code' => '1', 'message' => MESSAGE_REQUIRED . 'patientID.'];
        }
        if (empty($diagnosis))
        {
            return ['code' => '1', 'message' => MESSAGE_REQUIRED . 'diagnosis.'];
        }
        //$encodeDiagnosis = mb_convert_encoding($diagnosis, 'GBK', 'UTF-8');
        $arrayTemp = explode($separator, $diagnosis);
        $arrayDiagnosis = array_intersect($masterDiagnosis, $arrayTemp);
        $keys = array_keys($arrayDiagnosis);
        
        foreach ($keys as $key) {
            $ret = DbiAnalytics::getDbi()->addPatientDiagnosis($patientId, $key);
            if (VALUE_DB_ERROR === $ret) {
                return ['code' => '2', 'message' => MESSAGE_DB_ERROR];
            }
        }
        
        return ['code' => '0', 'message' => MESSAGE_SUCCESS];
    }
    
    /**
     * get patient list by diagnosis list.
     * @param string $diagnosis diagnosis list separated with ","(like '1,2,3' or '1').
     * @return array [code,message] | array [code,message,patients]
     */
    public static function getPatientsByDiagnosis($diagnosis)
    {
        $diagnosisList = ' (' . $diagnosis . ') ';
        $patients = DbiAnalytics::getDbi()->getPatientByDiagnosis($diagnosisList);
        if (VALUE_DB_ERROR === $ret) {
            return ['code' => '2', 'message' => MESSAGE_DB_ERROR];
        }
        if (empty($patients)) {
            return ['code' => '4', 'message' => MESSAGE_DB_NO_DATA];
        }
        
        foreach ($patients as $key => $row) {
            $patients[$key]['age'] = date('Y') - $row['birth_year'];
            $patients[$key]['sex'] = $row['sex'] == 1 ? '男' : '女';
            unset($patients[$key]['birth_year']);
        }
        return ['code' => '0', 'patients' => $patients];
    }
    
    public static function getDiagnosisByPatient($patientId)
    {
        //@todo add codes in future.
    }
}
