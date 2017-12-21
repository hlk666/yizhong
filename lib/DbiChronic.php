<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiChronic extends BaseDbi
{
    private static $instance;
    
    protected function __construct()
    {
        $this->db = 'chronic';
        $this->init();
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function getData($sql)
    {
        return $this->getDataAll($sql);
    }
    public function addEcgExamination($patientId, $guardianId, $url, $result)
    {
        if ($this->existData(examination_patient, "patient_id = $patientId and record_id = $guardianId and type = 'app'")) {
            $sql = "update examination_patient set examination_value = '$url', examination_result = '$result' 
                    where patient_id = $patientId and record_id = $guardianId and type = 'app'";
            return $this->updateData($sql);
        } else {
            $sql = "insert into examination_patient
                    (department_id, patient_id, record_id, type, examination_id, examination_value, examination_result)
                    values (0, $patientId, $guardianId, 'app', 21, '$url', '$result')";
            return $this->insertData($sql);
        }
    }
    public function getPatientByTel($tel)
    {
        $sql = "select id as patient_id from patient where tel = '$tel' limit 1";
        return $this->getDataString($sql);
    }
}
