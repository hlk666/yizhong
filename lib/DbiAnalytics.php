<?php
require_once PATH_LIB . 'Logger.php';
require_once PATH_LIB . 'BaseDbi.php';

class DbiAnalytics extends BaseDbi
{
    private static $instance;
    
    protected function __construct()
    {
        $this->init();
    }
    
    public static function getDbi()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function addPatientDiagnosis($patientId, $diagnosisId)
    {
        $sql = 'select 1 from patient_diagnosis where patient_id = :patient and diagnosis_id = :diagnosis limit 1';
        $param = [':patient' => $patientId, ':diagnosis' => $diagnosisId];
        $ret = $this->getDataRow($sql, $param);
        if (VALUE_DB_ERROR === $ret || !empty($ret)) {
            return;
        }
        
        $sql = 'insert into patient_diagnosis (patient_id, diagnosis_id) values (:patient, :diagnosis)';
        return $this->insertData($sql, $param);
    }
    
    public function getHospitalInfo($hospitalId)
    {
        $sql = 'select hospital_id, hospital_name, address, tel, parent_flag, sms_tel
                from hospital where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getHospitalTree($guardianId)
    {
        $sql = 'select regist_hospital_id from guardian where guardian_id = :guardian_id limit 1';
        $param = [':guardian_id' => $guardianId];
        $hospitalId = $this->getDataString($sql, $param);
        if (VALUE_DB_ERROR === $hospitalId || '' == $hospitalId) {
            return array();
        }
        
        $sql = 'select hospital_id, analysis_hospital, report_hospital from hospital_tree
                where hospital_id = :hospital_id limit 1';
        $param = [':hospital_id' => $hospitalId];
        return $this->getDataRow($sql, $param);
    }
    public function getPatient($guardianId)
    {
        $sql = 'select guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 where guardian_id = :guardian_id';
        $param = array(':guardian_id' => $guardianId);
        return $this->getDataRow($sql, $param);
    }
    public function getPatientsForAnalytics($hospitalId, $reported = null, $startTime = null, $endTime = null)
    {
        $sql = 'select guardian_id as patient_id, start_time, end_time, patient_name as name, birth_year, sex, tel, reported
                 from guardian as g left join patient as p on g.patient_id = p.patient_id
                 where regist_hospital_id = :hospital_id';
        $param = array(':hospital_id' => $hospitalId);
        if (isset($reported)) {
            $sql .= ' and reported = :reported ';
            $param[':reported'] = $reported;
        }
        if (isset($startTime)) {
            $sql .= ' and end_time >= :start_time ';
            $param[':start_time'] = $startTime;
        }
        if (isset($endTime)) {
            $sql .= ' and end_time <= :end_time ';
            $param[':end_time'] = $endTime;
        }
        $sql .= ' order by guardian_id desc';
        return $this->getDataAll($sql, $param);
    }
    public function addGuardianData($guardianId, $url)
    {
        if ($this->existData('guardian_data', 'guardian_id = ' . $guardianId)) {
            $sql = 'update guardian_data set url = :url, upload_time = now() where guardian_id = :guardian_id';
            $param = [':guardian_id' => $guardianId, ':url' => $url];
            return $this->updateData($sql, $param);
        }
        else {
            $sql = 'insert into guardian_data (guardian_id, url) values (:guardian_id, :url)';
            $param = [':guardian_id' => $guardianId, ':url' => $url ];
            return $this->insertData($sql, $param);
        }
    }
    public function uploadReport($guardianId, $file)
    {
        $sql = 'update guardian set reported = 1, report_file = :file where guardian_id = :guardian';
        $param = [':file' => $file, ':guardian' => $guardianId];
        return $this->updateData($sql, $param);
    }
}
